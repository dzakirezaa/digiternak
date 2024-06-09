<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models\Cage;
use yii\web\MethodNotAllowedHttpException;

class CageController extends ActiveController
{
    public $modelClass = 'app\models\Cage';

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
     * Menampilkan data Cage.
     * @return mixed
     */
    public function actionIndex()
    {
        $cages = Cage::find()->all();
        return $cages;
    }

    /**
     * Menampilkan data Cage berdasarkan ID.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $cage = Cage::findOne($id);

        if ($cage) {
            Yii::$app->response->statusCode = 200;
            return [
                'message' => 'Data kandang berhasil ditemukan',
                'error' => false,
                'data' => $cage,
            ];
        } else {
            Yii::$app->response->statusCode = 404;
            return [
                'message' => "Kandang tidak ditemukan",
                'error' => true,
            ];
        }
    }

    /**
     * Mendapatkan daftar nama kandang berdasarkan pengguna yang sedang login.
     * @return array
     */
    public function actionGetCages()
    {
        // Get the ID of the currently logged in user
        $userId = Yii::$app->user->identity->id;

        // Get the list of cages based on user_id
        $cages = Cage::find()
            ->where(['user_id' => $userId])
            ->all();

        if (!empty($cages)) {
            // If the query was successful, return the list of cages, a success message, and an error status of false
            Yii::$app->response->statusCode = 200;
            return [
                'message' => 'Berhasil mendapatkan daftar kandang',
                'error' => false,
                'data' => $cages,
            ];
        } else {
            // If the query failed, return an error message, an error status of true, and no cages found message
            Yii::$app->response->statusCode = 404;
            return [
                'message' => 'Tidak ada kandang yang ditemukan',
                'error' => true,
            ];
        }
    }

    /**
     * Membuat data Cage baru.
     * @return mixed
     */
    public function actionCreate()
    {
        $cage = new Cage();
        $cage->scenario = Cage::SCENARIO_CREATE;

        $cage->load(Yii::$app->request->getBodyParams(), '');
        $cage->user_id = Yii::$app->user->id;
        if ($cage->save()) {
            Yii::$app->response->statusCode = 201;
            return [
                'message' => 'Kandang berhasil dibuat',
                'error' => false,
                'data' => $cage,
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Gagal membuat kandang', 
                'error' => true, 
                'details' => $this->getValidationErrors($cage),
            ];
        }
    }

    /**
     * Mengupdate data Cage berdasarkan ID.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $cage = Cage::findOne($id);

        $cage->scenario = Cage::SCENARIO_UPDATE;

        $cage->load(Yii::$app->request->getBodyParams(), '');
        if ($cage->save()) {
            Yii::$app->response->statusCode = 200;
            return [
                'message' => 'Kandang berhasil diperbarui',
                'error' => false, 
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Gagal memperbarui kandang',
                'error' => true, 
                'details' => $this->getValidationErrors($cage),
            ];
        }
    }

    /**
     * Menghapus data Cage berdasarkan ID.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!ctype_digit($id)) {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Invalid ID',
                'error' => true,
            ];
        }

        $cage = Cage::findOne($id);

        if ($cage && $cage->delete()) {
            Yii::$app->response->statusCode = 200;
            return [
                'message' => 'Kandang berhasil dihapus',
                'error' => false,
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Gagal menghapus kandang',
                'error' => true,
            ];
        }
    }

    public function getValidationErrors($model)
    {
        $errorDetails = [];
        foreach ($model->errors as $errors) {
            foreach ($errors as $error) {
                $errorDetails[] = $error;
            }
        }
        return $errorDetails;
    }

    public function actionHandleRequest($id = null)
    {
        $request = Yii::$app->request;

        if ($request->isGet) {
            return $id ? $this->actionView($id) : $this->actionGetCages();
        }

        if ($request->isPost) {
            return $this->actionCreate();
        }

        if ($request->isPut || $request->isPatch) {
            return $this->actionUpdate($id);
        }

        if ($request->isDelete) {
            return $this->actionDelete($id);
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }
}
