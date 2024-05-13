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
        $model = $this->findModel($id);
        if ($model !== null) {
            return [
                'message' => 'Berhasil menemukan catatan.',
                'error' => false,
                'data' => $model,
            ];
        } else {
            return [
                'message' => 'Catatan dengan ID tersebut tidak ditemukan.',
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
            return [
                'message' => 'Data ternak tidak ditemukan.',
                'error' => true,
            ];
        }

        // Find the cage associated with the livestock
        $cage = Cage::findOne($livestock->cage_id);

        if (!$cage) {
            return [
                'message' => 'Kandang tidak ditemukan.',
                'error' => true,
            ];
        }

        // Set the attributes of the Note
        $model->livestock_id = $livestock->id;
        $model->livestock_vid = $livestock->vid;
        $model->livestock_name = $livestock->name;
        $model->livestock_cage = $cage->name;
        $model->location = $cage->location;
        $model->date_recorded = date('Y-m-d');

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
            $errorDetails = [];
            foreach ($model->errors as $errors) {
                foreach ($errors as $error) {
                    $errorDetails[] = $error;
                }
            }
            return [
                'message' => 'Catatan gagal dibuat.',
                'error' => true,
                'details' => $errorDetails,
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
            $errorDetails = [];
            foreach ($model->errors as $errors) {
                foreach ($errors as $error) {
                    $errorDetails[] = $error;
                }
            }
            return [
                'message' => 'Gagal memperbarui catatan.',
                'error' => true,
                'details' => $errorDetails,
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
                return [
                    'message' => 'Catatan tidak ditemukan.',
                    'error' => true,
                ];
            }

            // Delete note images first
            NoteImage::deleteAll(['note_id' => $id]);

            // Then delete the note
            $model->delete();

            $transaction->commit();

            return [
                'message' => 'Catatan berhasil dihapus.',
                'error' => false,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();

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
            return [
                'message' => 'Berhasil menemukan catatan.',
                'error' => false,
                'data' => $notes,
            ];
        } else {
            return [
                'message' => 'Tidak ada catatan yang ditemukan.',
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
            return [
                'message' => 'Catatan berhasil ditemukan.',
                'error' => false,
                'data' => $notes,
            ];
        } else {
            return [
                'message' => 'Tidak ada catatan yang ditemukan.',
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
            
                // Simpan informasi gambar ke dalam tabel note_images
                $noteImage = new NoteImage();
                $noteImage->note_id = $model->id;
                $noteImage->image_path = $uploadPath . $imageName;
                if (!$noteImage->save()) {
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
                    'note_images' => $uploadedImages,
                ],
            ];
        } else {
            return [
                'message' => 'Tidak ada gambar yang diunggah.',
                'error' => true,
            ];
        }
    }
}
