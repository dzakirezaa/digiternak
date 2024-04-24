<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
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

class NoteController extends ActiveController
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
        return $this->findModel($id);
    }

    protected function findModel($id)
    {
        $model = Note::findOne($id);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested note does not exist.');
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
            return [
                'message' => 'Livestock not found',
                'error' => true,
            ];
        }

        // Find the cage associated with the livestock
        $cage = Cage::findOne($livestock->cage_id);

        if (!$cage) {
            return [
                'message' => 'Cage not found',
                'error' => true,
            ];
        }

        // Set the attributes of the Note
        $model->livestock_id = $livestock->id;
        $model->livestock_vid = $livestock->vid;
        $model->livestock_cage = $cage->name;
        $model->location = $cage->location;
        $model->date_recorded = date('Y-m-d'); // Set the date_recorded to today's date

        // Load the data from the request body
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->validate()) {
            // Save the model
            if ($model->save()) {
                Yii::$app->getResponse()->setStatusCode(201);
                return [
                    'message' => 'Note created successfully',
                    'error' => false,
                    'data' => $model,
                ];
            } 
        } else {
            return [
                'message' => 'Failed to create note',
                'error' => true,
                'details' => $model->errors,
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

        // Load the data from the request body
        $data = Yii::$app->getRequest()->getBodyParams();

        // Only allow updating of livestock_feed, costs, and details
        $model->livestock_feed = $data['livestock_feed'] ?? $model->livestock_feed;
        $model->costs = $data['costs'] ?? $model->costs;
        $model->details = $data['details'] ?? $model->details;

        // Save the model
        if ($model->validate() && $model->save()) {
            Yii::$app->getResponse()->setStatusCode(200);
            return [
                'message' => 'Note updated successfully',
                'error' => false,
                'data' => $model,
            ];
        } else {
            return [
                'message' => 'Failed to update note',
                'error' => true,
                'details' => $model->errors,
            ];
        }
    }

    /**
     * Menghapus data Note berdasarkan ID.
     * @param integer $id
     * @throws NotFoundHttpException jika data Note tidak ditemukan
     * @throws ServerErrorHttpException jika data Note tidak dapat dihapus
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return [
            'message' => 'Note deleted successfully.',
            'error' => false,
        ];
    }

    /**
     * Mengembalikan semua data Note.
     * @return array
     */
    public function actionIndex()
    {
        return Note::find()->all();
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

            // Buat path direktori berdasarkan user_id dan id Livestock
            $uploadPath = 'uploads/notes/' . $userId . '/' . $model->id . '/';

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
                $noteImage = new NoteImage();
                $noteImage->note_id = $model->id;
                $noteImage->image_path = $uploadPath . $imageName;
                if (!$noteImage->save()) {
                    throw new ServerErrorHttpException('Failed to save the image to the database');
                }
            
                // Simpan nama file ke dalam array
                $uploadedImages[] = $uploadPath . $imageName;
            }

            // Jika penyimpanan model berhasil
            return [
                'message' => 'Documentation uploaded successfully',
                'error' => false,
                'data' => [
                    'livestock_images' => $uploadedImages,
                ],
            ];
        } else {
            return [
                'message' => 'No Documentation uploaded',
                'error' => true,
            ];
        }
    }
}
