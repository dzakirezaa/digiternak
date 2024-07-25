<?php

namespace app\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use app\models\Livestock;
use app\models\LivestockImage;
use app\models\Cage;
use app\models\Note;
use app\models\NoteImage;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\Acl;

class LivestockController extends BaseController
{
    public $modelClass = 'app\models\Livestock';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();

        // Disable default CRUD actions
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // authenticator untuk otentikasi
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options'], 
        ];

        // VerbFilter untuk memastikan setiap action hanya menerima HTTP method yang sesuai
        $behaviors['verbs'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
                'view' => ['GET'],
                'search' => ['GET'],
                'get-livestocks' => ['GET'],
                'upload-image' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * Membuat data Livestock baru.
     * @return mixed
     * @throws ServerErrorHttpException jika data Livestock tidak dapat disimpan
     */
    public function actionCreate()
    {
        $model = new Livestock();
        $requestData = Yii::$app->getRequest()->getBodyParams();
        $model->load($requestData, '');

        // Validasi cage_id berdasarkan user_id
        $cageId = $model->cage_id;
        $userId = Yii::$app->user->identity->id;

        if ($cageId === null) {
            Yii::$app->getResponse()->setStatusCode(400); // Bad Request
            return [
                'message' => 'Kandang tidak boleh kosong, mohon buat kandang terlebih dahulu.',
                'error' => true,
            ];
        }
    
        $existingCage = Cage::find()
            ->where(['id' => $cageId, 'user_id' => $userId])
            ->exists();
    
        if (!$existingCage) {
            Yii::$app->getResponse()->setStatusCode(400); // Bad Request
            return [
                'message' => 'Kandang tidak ditemukan, mohon buat kandang sebelum menambahkan ternak.',
                'error' => true,
            ];
        }

        if ($model->save()) {
            Yii::$app->getResponse()->setStatusCode(201);
            return [
                'message' => 'Data ternak berhasil dibuat.',
                'error' => false,
                'data' => $model
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(400);
            return [
                'message' => 'Gagal membuat data ternak.',
                'error' => true,
                'details' => $this->getValidationErrors($model),
            ];
        }
    }

    /**
     * Mengupdate data Livestock berdasarkan ID.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException jika data Livestock tidak ditemukan
     * @throws ServerErrorHttpException jika data Livestock tidak dapat diupdate
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Livestock::SCENARIO_UPDATE;
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        // Check if the cage exists
        $cageId = $model->cage_id;
        $cageExists = Cage::findOne($cageId);
        if (!$cageExists) {
            Yii::$app->response->statusCode = 404;
            return [
                'message' => 'Kandang tidak ditemukan.',
                'error' => true,
            ];
        }

        if ($model->save()) {
            Yii::$app->response->statusCode = 200;
            return [
                'message' => 'Data ternak berhasil diperbarui.',
                'error' => false,
                'data' => $model,
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Gagal memperbarui data ternak.',
                'error' => true,
                'details' => $this->getValidationErrors($model),
            ];
        }
    }

    /**
     * Menampilkan data Livestock berdasarkan ID.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException jika data Livestock tidak ditemukan
     */
    public function actionView($id)
    {
        $livestock = $this->findModel($id);

        if ($livestock) {
            Yii::$app->response->statusCode = 200;
            return [
                'message' => 'Data ternak berhasil ditemukan.',
                'error' => false,
                'data' => $livestock,
            ];
        } else {
            Yii::$app->response->statusCode = 404;
            return [
                'message' => "Ternak tidak ditemukan",
                'error' => true,
            ];
        }
    }

    /**
     * Deletes a Livestock model based on its primary key value.
     * If the deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Get the livestock
            $livestock = $this->findModel($id);

            // Check if the livestock exists
            if ($livestock === null) {
                Yii::$app->response->statusCode = 404;
                return [
                    'message' => 'Gagal menghapus data ternak. Data ternak tidak ditemukan.',
                    'error' => true,
                ];
            }

            // Get all notes for the livestock
            $notes = Note::find()->where(['livestock_id' => $id])->all();

            foreach ($notes as $note) {
                // Delete the associated note images first
                NoteImage::deleteAll(['note_id' => $note->id]);
            }

            // Then delete the notes
            Note::deleteAll(['livestock_id' => $id]);

            // Delete livestock images
            LivestockImage::deleteAll(['livestock_id' => $id]);

            // Then delete the livestock
            $livestock->delete();

            $transaction->commit();

            Yii::$app->response->statusCode = 204;
            return [
                'message' => 'Data ternak berhasil dihapus.',
                'error' => false,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Gagal menghapus data ternak. Alasan: ' . $e->getMessage());
        }
    }

    /**
     * Mencari data Livestock berdasarkan VID.
     * @param string $vid
     * @return array
     */
    public function actionSearch($vid)
    {
        // Validasi pola VID
        if (!preg_match('/^[A-Z]{3}\d{4}$/', $vid)) {
            return [
                'message' => 'Format Visual ID tidak valid. Gunakan format tiga huruf kapital diikuti empat angka. Contoh: ABC1234.',
                'error' => true,
            ];
        }

        $userId = Yii::$app->user->identity->id;
        $livestock = Livestock::find()->where(['vid' => $vid, 'user_id' => $userId])->all();

        if ($livestock) {
            Yii::$app->getResponse()->setStatusCode(200); // OK
            return [
                'message' => 'Data ternak berhasil ditemukan.',
                'error' => false,
                'data' => $livestock,
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(404); // Not Found
            return [
                'message' => 'Data ternak tidak ditemukan.',
                'error' => true,
            ];
        }
    }

    /**
     * Retrieves livestock data by user_id.
     * @param integer $user_id
     * @return mixed
     */
    public function actionGetLivestocks($user_id)
    {
        $livestocks = Livestock::find()->where(['user_id' => $user_id])->all();

        if (!empty($livestocks)) {
            Yii::$app->getResponse()->setStatusCode(200); // OK
            return [
                'message' => 'Data ternak berhasil ditemukan.',
                'error' => false,
                'data' => $livestocks,
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(404); // Not Found
            return [
                'message' => 'Data ternak tidak ditemukan.',
                'error' => true,
            ];
        }
    }

    /**
     * Mengunggah gambar untuk Livestock berdasarkan ID.
     * @param integer $id
     * @return mixed
     * @throws ServerErrorHttpException jika gambar tidak dapat disimpan
     */
    public function actionUploadImage($id)
    {
        // Find the Livestock model based on ID
        $model = $this->findModel($id);

        // Get the image from the request
        $imageFiles = UploadedFile::getInstancesByName('livestock_image');

        if (!empty($imageFiles)) {
            // Get the user_id of the currently logged in user
            $userId = Yii::$app->user->identity->id;

            // Create a directory path based on user_id and Livestock id
            $uploadPath = 'livestock/' . $userId . '/' . $model->id . '/';

            $uploadedImages = [];

            // Initialize the Google Cloud Storage client
            $storage = new StorageClient([
                'keyFilePath' => Yii::getAlias('@app/config/sa.json')
            ]);
            $bucket = $storage->bucket('digiternak1');

            // Iterate through each uploaded file
            foreach ($imageFiles as $index => $imageFile) {
                // Check if the temporary file path is set
                if (empty($imageFile->tempName)) {
                    Yii::$app->response->statusCode = 400;
                    return [
                        'message' => 'Gagal mengunggah gambar. Silakan coba lagi.',
                        'error' => true,
                    ];
                }

                // Generate a unique file name
                $imageName = Yii::$app->security->generateRandomString(12) . $index . '.' . $imageFile->getExtension();
            
                // Save the file to the directory
                $object = $bucket->upload(
                    file_get_contents($imageFile->tempName),
                    ['name' => $uploadPath . $imageName]
                );

                // Make the object publicly accessible
                $object->update(['acl' => []], ['predefinedAcl' => 'publicRead']);
            
                // Get the public URL of the object
                $publicUrl = sprintf('https://storage.googleapis.com/%s/%s', $bucket->name(), $uploadPath . $imageName);

                // Save the image information to the livestock_images table
                $livestockImage = new LivestockImage();
                $livestockImage->livestock_id = $model->id;
                $livestockImage->image_path = $uploadPath . $imageName;
                if (!$livestockImage->save()) {
                    Yii::$app->response->statusCode = 400;
                    return [
                        'message' => 'Gagal menyimpan data gambar ke database.',
                        'error' => true,
                    ];
                }
            
                // Save the public URL to the array
                $uploadedImages[] = $publicUrl;
            }

            // If the model saving is successful
            Yii::$app->response->statusCode = 201;
            return [
                'message' => 'Gambar berhasil diunggah.',
                'error' => false,
                'data' => [
                    'livestock_images' => $uploadedImages,
                ],
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Tidak ada gambar yang diunggah.',
                'error' => true,
            ];
        }
    }

    /**
     * Menemukan model Livestock berdasarkan ID.
     * @param integer $id
     * @return Livestock the loaded model
     * @throws NotFoundHttpException jika data Livestock tidak ditemukan
     */
    protected function findModel($id)
    {
        if (($model = Livestock::findOne($id)) !== null) {
            return $model;
        } else {
            return null;
        }
    }
}

// public function actionUploadImage($id)
    // {
    //     // Temukan model Livestock berdasarkan ID
    //     $model = $this->findModel($id);

    //     // Ambil gambar dari request
    //     $imageFiles = UploadedFile::getInstancesByName('livestock_image');

    //     if (!empty($imageFiles)) {
    //         // Ambil user_id dari pengguna yang sedang login
    //         $userId = Yii::$app->user->identity->id;

    //         // Buat path direktori berdasarkan user_id dan id Livestock
    //         $uploadPath = 'uploads/livestock/' . $userId . '/' . $model->id . '/';

    //         // Periksa apakah direktori sudah ada, jika tidak, buat direktori baru
    //         if (!is_dir($uploadPath)) {
    //             FileHelper::createDirectory($uploadPath);
    //         }

    //         $uploadedImages = [];

    //         // Iterasi melalui setiap file yang diunggah
    //         foreach ($imageFiles as $index => $imageFile) {
    //             // Generate nama file yang unik
    //             $imageName = Yii::$app->security->generateRandomString(12) . $index . '.' . $imageFile->getExtension();
            
    //             // Simpan file ke direktori
    //             $imageFile->saveAs($uploadPath . $imageName);
            
    //             // Save image information to the livestock_images table
    //             $livestockImage = new LivestockImage();
    //             $livestockImage->livestock_id = $model->id;
    //             $livestockImage->image_path = $uploadPath . $imageName;
    //             if (!$livestockImage->save()) {
    //                 Yii::$app->response->statusCode = 400;
    //                 return [
    //                     'message' => 'Gagal menyimpan data gambar ke database.',
    //                     'error' => true,
    //                 ];
    //             }
            
    //             // Simpan nama file ke dalam array
    //             $uploadedImages[] = $uploadPath . $imageName;
    //         }

    //         // Jika penyimpanan model berhasil
    //         Yii::$app->response->statusCode = 201;
    //         return [
    //             'message' => 'Gambar berhasil diunggah.',
    //             'error' => false,
    //             'data' => [
    //                 'livestock_images' => $uploadedImages,
    //             ],
    //         ];
    //     } else {
    //         Yii::$app->response->statusCode = 400;
    //         return [
    //             'message' => 'Tidak ada gambar yang diunggah.',
    //             'error' => true,
    //         ];
    //     }
    // }

    /**
     * Mengembalikan semua data Livestock.
     * @return array
     */
    // public function actionIndex()
    // {
    //     $livestocks = Livestock::find()->all();
        
    //     if (!empty($livestocks)) {
    //         Yii::$app->response->statusCode = 200;
    //         return $livestocks;
    //     } else {
    //         Yii::$app->getResponse()->setStatusCode(404); // Not Found
    //         return [
    //             'message' => 'Ternak tidak ditemukan.',
    //             'error' => true,
    //         ];
    //     }
    // }