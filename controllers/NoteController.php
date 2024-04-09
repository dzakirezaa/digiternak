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
    public function actionCreate()
    {
        $model = new Note();

        // Ambil nilai livestock_vid dari nilai vid pada tabel Livestock
        $livestock_vid = Yii::$app->getRequest()->getBodyParam('livestock_vid');
        $livestock = Livestock::findOne(['vid' => $livestock_vid]);

        if (!$livestock) {
            return [
                'message' => 'Livestock not found',
                'error' => true,
            ];
        }

        // Ambil person_id yang terkait dengan livestock
        $person_id = $livestock->person_id;

        // Pengecekan apakah person_id memiliki cage yang sesuai
        $livestock_cage = Yii::$app->getRequest()->getBodyParam('livestock_cage');
        $cage = Cage::findOne(['name' => $livestock_cage, 'person_id' => $person_id]);

        if (!$cage) {
            return [
                'message' => 'Cage not found',
                'error' => true,
            ];
        }

        // Ambil nilai tanggal dari input
        $date_recorded_input = Yii::$app->getRequest()->getBodyParam('date_recorded');

        // Konversi format tanggal ke format yang dapat diterima oleh MySQL
        $date_recorded_mysql = date('Y-m-d', strtotime($date_recorded_input));

        // Set nilai atribut-atribut Note
        $model->livestock_vid = $livestock->vid;

        // Set nilai atribut-atribut Cage
        $model->livestock_cage = $cage->name;

        // Set nilai atribut date_recorded dengan format yang sesuai
        $model->date_recorded = $date_recorded_mysql;

        // Set aturan validasi untuk date_recorded
        $model->rules()[] = [['date_recorded'], 'compare', 'compareValue' => date('Y-m-d'), 'operator' => '<=', 'message' => '{attribute} must be today or before today'];

        // Memuat data dari body permintaan ke model
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->save()) {
            Yii::$app->getResponse()->setStatusCode(201);
            return [
                'message' => 'Note created successfully',
                'error' => false,
                'data' => $model,
            ];
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

        // Ambil nilai livestock_vid dari nilai vid pada tabel Livestock
        $livestock_vid = Yii::$app->getRequest()->getBodyParam('livestock_vid');
        $livestock = Livestock::findOne(['vid' => $livestock_vid]);

        if (!$livestock) {
            return [
                'message' => 'Livestock not found',
                'error' => true,
            ];
        }

        // Ambil person_id yang terkait dengan livestock
        $person_id = $livestock->person_id;

        // Pengecekan apakah person_id memiliki cage yang sesuai
        $livestock_cage = Yii::$app->getRequest()->getBodyParam('livestock_cage');
        $cage = Cage::findOne(['name' => $livestock_cage, 'person_id' => $person_id]);

        if (!$cage) {
            return [
                'message' => 'Cage not found',
                'error' => true,
            ];
        }

        // Ambil nilai tanggal dari input
        $date_recorded_input = Yii::$app->getRequest()->getBodyParam('date_recorded');

        // Konversi format tanggal ke format yang dapat diterima oleh MySQL
        $date_recorded_mysql = date('Y-m-d', strtotime($date_recorded_input));

        // Set nilai atribut-atribut Note
        $model->livestock_vid = $livestock->vid;

        // Set nilai atribut-atribut Cage
        $model->livestock_cage = $cage->name;

        // Set nilai atribut date_recorded dengan format yang sesuai
        $model->date_recorded = $date_recorded_mysql;

        // Memuat data dari body permintaan ke model
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->save()) {
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
            // Ambil person_id dari pengguna yang sedang login
            $personId = Yii::$app->user->identity->person_id;

            // Buat path direktori berdasarkan person_id dan id Livestock
            $uploadPath = 'uploads/notes/' . $personId . '/' . $model->id . '/';

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
