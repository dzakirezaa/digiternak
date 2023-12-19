<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\filters\auth\HttpBearerAuth;
use app\models\Note;

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

    public function actionCreate()
    {
        $model = new Note();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
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

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        // Update nilai 'updated_at' dengan waktu sekarang
        // $model->updated_at = Yii::$app->formatter->asDatetime(time());

        if ($model->save()) {
            return [
                'status' => 'success',
                'message' => 'Note updated successfully.',
                'data' => $model,
            ];
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the note for unknown reason.');
        } else {
            throw new BadRequestHttpException('Failed to update the note due to validation error.', 422);
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

    protected function findModel($id)
    {
        $model = Note::findOne($id);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested note does not exist.');
        }
    }
}
