<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use app\models\Livestock;

class LivestockController extends ActiveController
{
    public $modelClass = 'app\models\Livestock';

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
     * Menampilkan data Livestock berdasarkan ID.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException jika data Livestock tidak ditemukan
     */
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    /**
     * Membuat data Livestock baru.
     * @return mixed
     * @throws BadRequestHttpException jika input tidak valid
     * @throws ServerErrorHttpException jika data Livestock tidak dapat disimpan
     */
    public function actionCreate()
    {
        $model = new Livestock();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            Yii::$app->getResponse()->setStatusCode(201);
            return [
                'status' => 'success',
                'message' => 'Data Livestock berhasil dibuat.',
                'data' => $model,
            ];
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        } else {
            throw new BadRequestHttpException('Failed to create the object due to validation error.', 422);
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
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            return [
                'status' => 'success',
                'message' => 'Data Livestock berhasil diperbarui.',
                'data' => $model,
            ];
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        } else {
            throw new BadRequestHttpException('Failed to update the object due to validation error.', 422);
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
            'status' => 'success',
            'message' => 'Data Livestock berhasil dihapus.',
        ];
    }

    /**
     * Mengembalikan semua data Livestock.
     * @return array
     */
    public function actionIndex()
    {
        return Livestock::find()->all();
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
            throw new NotFoundHttpException('The requested object does not exist.');
        }
    }

    /**
     * Mencari data Livestock berdasarkan VID.
     * @param string $vid
     * @return array
     */
    public function actionSearch($vid)
    {
        $livestock = Livestock::find()->where(['vid' => $vid])->all();

        if ($livestock) {
            return [
                'status' => 'success',
                'message' => 'Data Livestock ditemukan.',
                'data' => $livestock,
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Data Livestock tidak ditemukan.',
            ];
        }
    }
}
