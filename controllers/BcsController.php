<?php

namespace app\controllers;

use Yii;
use app\models\BodyCountScore;
use app\models\Livestock;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use app\models\BcsImage;
use Google\Cloud\Storage\StorageClient;

class BcsController extends BaseController
{
    public $modelClass = 'app\models\BodyCountScore';

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
            'class' => VerbFilter::class,
            'actions' => [
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
                'view' => ['GET'],
                'get-bcs-by-livestock-id' => ['GET'],
                'upload-bcs' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    public function actionCreate($livestock_id)
    {
        $model = new BodyCountScore();
        $model->livestock_id = $livestock_id;
    
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            // Update the Livestock model
            $livestock = Livestock::findOne($model->livestock_id);
            if ($livestock) {
                $model->save();
                $livestock->body_weight = $model->body_weight;
                $livestock->chest_size = $model->chest_size;
                $livestock->save(false);
    
                Yii::$app->response->setStatusCode(201);
                return [
                    'message' => 'BCS berhasil dibuat.',
                    'error' => false,
                    'data' => $model,
                ];
            } else {
                Yii::$app->response->setStatusCode(400);
                return [
                    'message' => 'Gagal membuat pembaruan BCS. Ternak tidak ditemukan',
                    'error' => true,
                ];
            }
        } else {
            Yii::$app->response->setStatusCode(400);
            return [
                'message' => 'Gagal membuat BCS. Data tidak valid.',
                'error' => true,
                'details' => $this->getValidationErrors($model),
            ];
        }
    }

    public function actionUpdate($id)
    {
        $model = BodyCountScore::findOne($id);
        if ($model) {
            if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
                // Update the Livestock model
                $livestock = Livestock::findOne($model->livestock_id);
                if ($livestock) {
                    $model->save();
                    $livestock->body_weight = $model->body_weight;
                    $livestock->chest_size = $model->chest_size;
                    $livestock->save(false);

                    Yii::$app->response->setStatusCode(200);
                    return [
                        'message' => 'BCS berhasil diperbarui.',
                        'error' => false,
                        'data' => $model,
                    ];
                } else {
                    Yii::$app->response->setStatusCode(400);
                    return [
                        'message' => 'Gagal memperbarui BCS. Ternak tidak ditemukan',
                        'error' => true,
                    ];
                }
            } else {
                Yii::$app->response->setStatusCode(400);
                return [
                    'message' => 'Gagal memperbarui BCS. Data tidak valid.',
                    'error' => true,
                    'details' => $this->getValidationErrors($model),
                ];
            }
        } else {
            Yii::$app->response->setStatusCode(400);
            return [
                'message' => 'Gagal memperbarui BCS, data tidak ditemukan.',
                'error' => true,
            ];
        }
    }

    public function actionDelete($id)
    {
        $model = BodyCountScore::findOne($id);
        if ($model) {
            $model->delete();
            $response = Yii::$app->response;
            $response->setStatusCode(204);
            $response->data = [
                'message' => 'Data BCS berhasil dihapus.',
                'error' => false,
            ];
            return $response;
        } else {
            $response = Yii::$app->response;
            $response->setStatusCode(404);
            $response->data = [
                'message' => 'Gagal menghapus data BCS, data tidak ditemukan.',
                'error' => true,
            ];
            return $response;
        }
    }

    public function actionView($id)
    {
        $model = BodyCountScore::findOne($id);
        if ($model) {
            $response = Yii::$app->response;
            $response->setStatusCode(200);
            $response->data = [
                'message' => 'Data BCS berhasil ditemukan.',
                'error' => false,
                'data' => $model,
            ];
            return $response;
        } else {
            $response = Yii::$app->response;
            $response->setStatusCode(404);
            $response->data = [
                'message' => 'Data BCS tidak ditemukan.',
                'error' => true,
            ];
            return $response;
        }
    }

    public function actionGetBcsByLivestockId($livestock_id)
    {
        $models = BodyCountScore::find()->where(['livestock_id' => $livestock_id])->all();
        if ($models) {
            $response = Yii::$app->response;
            $response->setStatusCode(200);
            $response->data = [
                'message' => 'Data BCS berhasil ditemukan.',
                'error' => false,
                'data' => $models,
            ];
            return $response;
        } else {
            $response = Yii::$app->response;
            $response->setStatusCode(404);
            $response->data = [
                'message' => 'Data BCS tidak ditemukan.',
                'error' => true,
            ];
            return $response;
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
    public function actionUploadBcs($id)
    {
        // Find the Bcs model based on ID
        $model = $this->findModel($id);

        // Get the image from the request
        $imageFiles = UploadedFile::getInstancesByName('bcs_image');

        if (!empty($imageFiles)) {
            // Get the user_id of the currently logged in user
            $userId = Yii::$app->user->identity->id;

            // Create a directory path based on user_id and Livestock id
            $uploadPath = 'bcs/' . $userId . '/' . $model->livestock_id . '/' . $model->id . '/';

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

                // Save the image information to the bcs_images table
                $bcsImage = new BcsImage();
                $bcsImage->bcs_id = $model->id;
                $bcsImage->image_path = $uploadPath . $imageName;
                if (!$bcsImage->save()) {
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

    protected function findModel($id)
    {
        if (($model = BodyCountScore::findOne($id)) !== null) {
            return $model;
        } else {
            return null;
        }
    }

}