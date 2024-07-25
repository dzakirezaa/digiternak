<?php

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\filters\auth\HttpBearerAuth;
use app\models\Note;
use app\models\NoteImage;
use app\models\Livestock;
use app\models\Cage;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use Google\Cloud\Storage\StorageClient;

class NoteController extends BaseController
{
    public $modelClass = 'app\models\Note';

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
            'except' => ['options'], 
        ];

        // Menambahkan VerbFilter untuk memastikan setiap action hanya menerima HTTP method yang sesuai
        $behaviors['verbs'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
                'view' => ['GET'],
                'index' => ['GET'],
                'get-note-by-livestock-id' => ['GET'],
                'upload-documentation' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * Menampilkan data Note berdasarkan ID.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException jika data Note tidak ditemukan
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model !== null) {
            Yii::$app->response->statusCode = 200;
            return [
                'message' => 'Berhasil menemukan catatan.',
                'error' => false,
                'data' => $model,
            ];
        } else {
            Yii::$app->response->statusCode = 404;
            return [
                'message' => 'Catatan tidak ditemukan.',
                'error' => true,
            ];
        }
    }

    protected function findModel($id)
    {
        if (($model = Note::findOne($id)) !== null) {
            return $model;
        } else {
            return null;
        }
    }

    /**
     * Membuat data Note baru.
     * @return mixed
     * @throws BadRequestHttpException jika input tidak valid
     * @throws ServerErrorHttpException jika data Note tidak dapat disimpan
     */
    public function actionCreate($livestock_id)
    {
        $model = new Note();

        // Find the livestock using the provided livestock_id
        $livestock = Livestock::findOne($livestock_id);

        if (!$livestock) {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Gagal membuat catatan, data ternak tidak ditemukan.',
                'error' => true,
            ];
        }

        // Find the cage associated with the livestock
        $cage = Cage::findOne($livestock->cage_id);

        if (!$cage) {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Gagal membuat catatan, kandang tidak ditemukan.',
                'error' => true,
            ];
        }

        // Set the attributes of the Note
        $model->livestock_id = $livestock->id;
        $model->livestock_vid = $livestock->vid;
        $model->livestock_name = $livestock->name;
        $model->livestock_cage = $cage->name;
        $model->location = $cage->location;
        
        // Load the data from the request body
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->validate()) {
            // Save the model
            if ($model->save()) {
                Yii::$app->getResponse()->setStatusCode(201);
                return [
                    'message' => 'Catatan berhasil dibuat.',
                    'error' => false,
                    'data' => $model,
                ];
            } 
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Catatan gagal dibuat.',
                'error' => true,
                'details' => $this->getValidationErrors($model),
            ];
        }
    }

    /**
     * Mengupdate data Note berdasarkan ID.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException jika data Note tidak ditemukan
     * @throws BadRequestHttpException jika input tidak valid
     * @throws ServerErrorHttpException jika data Note tidak dapat disimpan
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model === null) {
            Yii::$app->getResponse()->setStatusCode(404);
            return [
                'message' => 'Catatan tidak ditemukan.',
                'error' => true,
            ];
        }

        // Load the data from the request body
        $data = Yii::$app->getRequest()->getBodyParams();

        // Only allow updating of livestock_feed, costs, and details
        $model->livestock_feed = $data['livestock_feed'] ?? $model->livestock_feed;
        $model->feed_weight = $data['feed_weight'] ?? $model->feed_weight;
        $model->vitamin = $data['vitamin'] ?? $model->vitamin;
        $model->costs = $data['costs'] ?? $model->costs;
        $model->details = $data['details'] ?? $model->details;

        // Save the model
        if ($model->validate() && $model->save()) {
            Yii::$app->getResponse()->setStatusCode(200);
            return [
                'message' => 'Catatan berhasil diperbarui.',
                'error' => false,
                'data' => $model,
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(400);
            return [
                'message' => 'Gagal memperbarui catatan.',
                'error' => true,
                'details' => $this->getValidationErrors($model),
            ];
        }
    }

    /**
     * Deletes a Note model based on its primary key value.
     * If the deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ServerErrorHttpException if the model cannot be deleted
     */
    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = $this->findModel($id);

            if ($model === null) {
                Yii::$app->response->statusCode = 404;
                return [
                    'message' => 'Gagal menghapus catatan, catatan tidak ditemukan.',
                    'error' => true,
                ];
            }

            // Delete note images first
            NoteImage::deleteAll(['note_id' => $id]);

            // Then delete the note
            $model->delete();

            $transaction->commit();

            Yii::$app->response->statusCode = 204;
            return [
                'message' => 'Catatan berhasil dihapus.',
                'error' => false,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();

            Yii::$app->response->statusCode = 500;
            return [
                'message' => 'Gagal menghapus catatan : ' . $e->getMessage(),
                'error' => true,
            ];
        }
    }

    /**
     * Returns all notes created by the current user.
     * @return array
     */
    public function actionIndex()
    {
        $notes = Note::find()->where(['user_id' => Yii::$app->user->id])->all();

        if (!empty($notes)) {
            Yii::$app->response->statusCode = 200;
            return [
                'message' => 'Berhasil menemukan catatan.',
                'error' => false,
                'data' => $notes,
            ];
        } else {
            Yii::$app->response->statusCode = 404;
            return [
                'message' => 'Catatan tidak ditemukan.',
                'error' => true,
            ];
        }
    }

    /**
     * Get note data by livestock_id.
     * @param integer $livestock_id
     * @return mixed
     */
    public function actionGetNoteByLivestockId($livestock_id)
    {
        $notes = Note::find()->where(['livestock_id' => $livestock_id])->all();

        if (!empty($notes)) {
            Yii::$app->response->statusCode = 200;
            return [
                'message' => 'Catatan berhasil ditemukan.',
                'error' => false,
                'data' => $notes,
            ];
        } else {
            Yii::$app->response->statusCode = 404;
            return [
                'message' => 'Catatan tidak ditemukan.',
                'error' => true,
            ];
        }
    }

    /**
     * Mengunggah dokumentasi ke dalam catatan.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException jika data Note tidak ditemukan
     * @throws BadRequestHttpException jika tidak ada dokumentasi yang diunggah
     * @throws ServerErrorHttpException jika data Note tidak dapat disimpan
     */
    public function actionUploadDocumentation($id)
    {
        // Temukan model Note berdasarkan ID
        $model = $this->findModel($id);

        // Ambil gambar dari request
        $imageFiles = UploadedFile::getInstancesByName('documentation');

        if (!empty($imageFiles)) {
            // Ambil user_id dari pengguna yang sedang login
            $userId = Yii::$app->user->identity->id;

            // Buat path direktori berdasarkan user_id dan id Note
            $uploadPath = 'notes/' . $userId . '/' . $model->livestock_id . '/' . $model->id . '/';

            // Periksa apakah direktori sudah ada, jika tidak, buat direktori baru
            if (!is_dir($uploadPath)) {
                FileHelper::createDirectory($uploadPath);
            }

            $uploadedImages = [];

            // Initialize the Google Cloud Storage client
            $storage = new StorageClient([
                'keyFilePath' => Yii::getAlias('@app/config/sa.json')
            ]);
            $bucket = $storage->bucket('digiternak1');

            // Iterate through each uploaded file
            foreach ($imageFiles as $index => $imageFile) {
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

                // Save the image information to the note_images table
                $noteImage = new NoteImage();
                $noteImage->note_id = $model->id;
                $noteImage->image_path = $uploadPath . $imageName;
                if (!$noteImage->save()) {
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
}
