<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\filters\auth\HttpBearerAuth;
use app\models\Note;
use app\models\Livestock;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class NoteController extends ActiveController
{
    public $modelClass = 'app\models\Note';

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

        // // Menentukan bahwa parser form-data hanya akan digunakan untuk actionUploadDocumentation
        // $behaviors['parsers'] = [
        //     'application/json' => 'yii\web\JsonParser', 
        //     'multipart/form-data' => 'yii\web\MultipartFormDataParser', 
        // ];

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
    public function actionCreate()
    {
        $model = new Note();

        // Ambil data Livestock yang terkait
        $livestock_vid = Yii::$app->getRequest()->getBodyParam('livestock_vid');
        $livestock_cage = Yii::$app->getRequest()->getBodyParam('livestock_cage');
        $livestock = Livestock::findOne(['vid' => $livestock_vid, 'cage' => $livestock_cage]);
        if (!$livestock) {
            throw new NotFoundHttpException('Livestock not found.');
        }

        // Set atribut-atribut Note
        $model->livestock_vid = $livestock_vid;
        $model->livestock_cage = $livestock_cage;
        $model->date_recorded = date('Y-m-d');
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        // Ambil file dokumentasi jika ada
        $documentation = UploadedFile::getInstanceByName('documentation');
        if ($documentation !== null) {
            $model->documentation = $documentation->name;
        }

        // Simpan catatan
        if ($model->save()) {
            Yii::$app->getResponse()->setStatusCode(201);
            return [
                'status' => 'success',
                'message' => 'Note created successfully.',
                'data' => $model,
            ];
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the note for unknown reason.');
        } else {
            throw new BadRequestHttpException('Failed to create the note due to validation error.', 422);
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
            'status' => 'success',
            'message' => 'Data Note berhasil dihapus.',
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
        
        // Ambil file-file yang diunggah
        $documentationFiles = UploadedFile::getInstancesByName('documentation');
        
        if (!empty($documentationFiles)) {
            // Directory untuk menyimpan dokumentasi
            $uploadPath = 'uploads/note/';

            // Membuat direktori jika belum ada
            if (!is_dir($uploadPath)) {
                FileHelper::createDirectory($uploadPath);
            }

            foreach ($documentationFiles as $file) {
                // Generate nama file yang unik
                $fileName = Yii::$app->security->generateRandomString(12) . '.' . $file->getExtension();
                
                // Simpan file ke direktori
                $file->saveAs($uploadPath . $fileName);
                
                // Simpan nama file ke dalam atribut di model Note
                $model->documentation = $fileName;
            }

            // Jika penyimpanan model berhasil
            if ($model->save()) {
                return [
                    'status' => 'success',
                    'message' => 'Documentation uploaded successfully.',
                    'data' => $model,
                ];
            } else {
                throw new ServerErrorHttpException('Failed to save the documentation to the database.');
            }
        } else {
            throw new BadRequestHttpException('No documentation uploaded.');
        }
    }
}
