<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use app\models\Livestock;
use app\models\LivestockImage;
use app\models\Cage;
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
                'message' => 'Livestocks not found',
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
        return [
            'message' => 'Livestock data found successfully',
            'error' => false,
            'data' => $this->findModel($id),
        ];
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

        if ($cageId !== null) {
            $existingCage = Cage::find()
                ->where(['id' => $cageId, 'user_id' => $userId])
                ->exists();

            if (!$existingCage) {
                return [
                    'message' => 'Cage not found, please create a cage before adding livestock',
                    'error' => true,
                ];
            }
        }

        if ($model->save()) {
            Yii::$app->getResponse()->setStatusCode(201);
            return [
                'message' => 'Livestock created successfully',
                'error' => false,
                'data' => $model
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(422); // Unprocessable Entity
            return [
                'message' => 'Failed to create Livestock',
                'error' => true,
                'details' => $model->getErrors(),
            ];
        }
    }

    /**
     * Memperbarui data Livestock berdasarkan ID.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException jika data Livestock tidak ditemukan
     * @throws BadRequestHttpException jika input tidak valid
     * @throws ServerErrorHttpException jika data Livestock tidak dapat disimpan
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            return [
                'message' => 'Livestock updated successfully',
                'error' => false,
                'data' => $model,
            ];
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason');
        } else {
            $errors = [];
            foreach ($model->getErrors() as $attribute => $error) {
                $errors[$attribute] = $error[0];
            }
            return [
                'message' => 'Livestock failed to update',
                'error' => true,
                'details' => $errors,
            ];
        }
    }

    /**
     * Menghapus data Livestock berdasarkan ID.
     * @param integer $id
     * @throws NotFoundHttpException jika data Livestock tidak ditemukan
     * @throws ServerErrorHttpException jika data Livestock tidak dapat dihapus
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return [
            'message' => 'Livestock deleted successfully',
            'error' => false,
        ];
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
                'message' => 'VID must follow the pattern of three uppercase letters followed by four digits',
                'error' => true,
            ];
        }

        $livestock = Livestock::find()->where(['vid' => $vid])->all();

        if ($livestock) {
            return [
                'message' => 'Livestock data found successfully',
                'error' => false,
                'data' => $livestock,
            ];
        } else {
            return [
                'message' => 'Livestock data not found',
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
                'message' => 'Livestock data found successfully',
                'error' => false,
                'data' => $livestocks,
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(404); // Not Found
            return [
                'message' => 'Livestock data not found',
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
            
                // Simpan informasi gambar ke dalam tabel livestock_images
                $livestockImage = new LivestockImage();
                $livestockImage->livestock_id = $model->id;
                $livestockImage->image_path = $uploadPath . $imageName;
                if (!$livestockImage->save()) {
                    throw new ServerErrorHttpException('Failed to save the image to the database');
                }
            
                // Simpan nama file ke dalam array
                $uploadedImages[] = $uploadPath . $imageName;
            }

            // Jika penyimpanan model berhasil
            return [
                'message' => 'Images uploaded successfully',
                'error' => false,
                'data' => [
                    'livestock_images' => $uploadedImages,
                ],
            ];
        } else {
            return [
                'message' => 'No images uploaded',
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
            throw new NotFoundHttpException('The requested object does not exist');
        }
    }
}
