<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models\Cage;

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
            return [
                'message' => 'Data kandang berhasil ditemukan',
                'error' => false,
                'data' => $cage,
            ];
        } else {
            return [
                'message' => "kandang dengan ID $id tidak ditemukan",
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
            return [
                'message' => 'Berhasil mendapatkan daftar kandang',
                'error' => false,
                'data' => $cages,
            ];
        } else {
            // If the query failed, return an error message, an error status of true, and no cages found message
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
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            $errorDetails = [];
            foreach ($cage->errors as $errors) {
                foreach ($errors as $error) {
                    $errorDetails[] = $error;
                }
            }
            return [
                'message' => 'Gagal membuat kandang', 
                'error' => true, 
                'details' => $errorDetails,
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
            $errorDetails = [];
            foreach ($cage->errors as $errors) {
                foreach ($errors as $error) {
                    $errorDetails[] = $error;
                }
            }
            return [
                'message' => 'Gagal memperbarui kandang',
                'error' => true, 
                'details' => $errorDetails,
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
        $cage = Cage::findOne($id);

        if ($cage->delete()) {
            Yii::$app->response->statusCode = 204;
            return [
                'message' => 'Kandang berhasil dihapus',
                'error' => false,
            ];
        } else {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => 'Gagal menghapus kandang',
                'error' => true, 
            ];
        }
    }
}
