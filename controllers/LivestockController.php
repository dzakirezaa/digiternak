<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
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

class LivestockController extends ActiveController
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

        // Menambahkan authenticator untuk otentikasi menggunakan access token
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options'], // Tambahkan action yang tidak memerlukan otentikasi di sini
        ];

        return $behaviors;
    }

    /**
     * Mengembalikan semua data Livestock.
     * @return array
     */
    public function actionIndex()
    {
        $livestocks = Livestock::find()->all();
        
        if (!empty($livestocks)) {
            return $livestocks;
        } else {
            Yii::$app->getResponse()->setStatusCode(404); // Not Found
            return [
                'message' => 'Ternak tidak ditemukan.',
                'error' => true,
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
            return [
                'message' => 'Data ternak berhasil ditemukan.',
                'error' => false,
                'data' => $livestock,
            ];
        } else {
            return [
                'message' => "Ternak dengan ID tersebut tidak ditemukan",
                'error' => true,
            ];
        }
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
            return [
                'message' => 'Kandang tidak boleh kosong, mohon buat kandang terlebih dahulu.',
                'error' => true,
            ];
        }
    
        $existingCage = Cage::find()
            ->where(['id' => $cageId, 'user_id' => $userId])
            ->exists();
    
        if (!$existingCage) {
            return [
                'message' => 'Kandang tidak dapat ditemukan, mohon buat kandang sebelum menambahkan ternak.',
                'error' => true,
            ];
        }

        if ($model->save()) {
            Yii::$app->getResponse()->setStatusCode(200);
            return [
                'message' => 'Data ternak berhasil dibuat.',
                'error' => false,
                'data' => $model
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(500); // Unprocessable Entity
            $errorDetails = [];
            foreach ($model->getErrors() as $errors) {
                foreach ($errors as $error) {
                    $errorDetails[] = $error;
                }
            }
            return [
                'message' => 'Gagal membuat data ternak.',
                'error' => true,
                'details' => $errorDetails,
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

        if ($model->save()) {
            Yii::$app->response->statusCode = 200;
            return [
                'message' => 'Data ternak berhasil diperbarui.',
                'error' => false,
                'data' => $model,
            ];
        } else {
            Yii::$app->response->statusCode = 500;
            $errorDetails = [];
            foreach ($model->getErrors() as $errors) {
                foreach ($errors as $error) {
                    $errorDetails[] = $error;
                }
            }
            return [
                'message' => 'Gagal memperbarui data ternak.',
                'error' => true,
                'details' => $errorDetails,
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
            $this->findModel($id)->delete();

            $transaction->commit();

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

        $livestock = Livestock::find()->where(['vid' => $vid])->all();

        if ($livestock) {
            return [
                'message' => 'Data ternak berhasil ditemukan.',
                'error' => false,
                'data' => $livestock,
            ];
        } else {
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
        // Temukan model Livestock berdasarkan ID
        $model = $this->findModel($id);

        // Ambil gambar dari request
        $imageFiles = UploadedFile::getInstancesByName('livestock_image');

        if (!empty($imageFiles)) {
            // Ambil user_id dari pengguna yang sedang login
            $userId = Yii::$app->user->identity->id;

            // Buat path direktori berdasarkan user_id dan id Livestock
            $uploadPath = 'uploads/livestock/' . $userId . '/' . $model->id . '/';

            // Periksa apakah direktori sudah ada, jika tidak, buat direktori baru
            if (!is_dir($uploadPath)) {
                FileHelper::createDirectory($uploadPath);
            }

            $uploadedImages = [];

            // Iterasi melalui setiap file yang diunggah
            foreach ($imageFiles as $index => $imageFile) {
                // Generate nama file yang unik
                $imageName = Yii::$app->security->generateRandomString(12) . $index . '.' . $imageFile->getExtension();
            
                // Simpan file ke direktori
                $imageFile->saveAs($uploadPath . $imageName);
            
                // Save image information to the livestock_images table
                $livestockImage = new LivestockImage();
                $livestockImage->livestock_id = $model->id;
                $livestockImage->image_path = $uploadPath . $imageName;
                if (!$livestockImage->save()) {
                    return [
                        'message' => 'Gagal menyimpan data gambar ke database.',
                        'error' => true,
                    ];
                }
            
                // Simpan nama file ke dalam array
                $uploadedImages[] = $uploadPath . $imageName;
            }

            // Jika penyimpanan model berhasil
            return [
                'message' => 'Gambar berhasil diunggah.',
                'error' => false,
                'data' => [
                    'livestock_images' => $uploadedImages,
                ],
            ];
        } else {
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
