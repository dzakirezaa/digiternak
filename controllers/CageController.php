<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
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
        return $cage;
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
            // ->select(['name'])
            ->where(['user_id' => $userId])
            // ->asArray()
            ->all();

        // Return the list of cage names in JSON format
        // return ArrayHelper::getColumn($cages, 'name');

        // Return the list of cages in JSON format
        return $cages;
    }

    /**
     * Membuat data Cage baru.
     * @return mixed
     */
    public function actionCreate()
    {
        $cage = new Cage();

        $cage->load(Yii::$app->request->getBodyParams(), '');
        if ($cage->save()) {
            Yii::$app->response->statusCode = 201;
            return [
                'message' => 'Cage created successfully',
                'error' => false, 
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Failed to create cage', 
                'error' => true, 
                'details' => $cage->errors
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

        $cage->load(Yii::$app->request->getBodyParams(), '');
        if ($cage->save()) {
            Yii::$app->response->statusCode = 200;
            return [
                'message' => 'Cage updated successfully',
                'error' => false, 
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Failed to update cage',
                'error' => true, 
                'details' => $cage->errors,
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
                'message' => 'Cage deleted successfully',
                'error' => false,
            ];
        } else {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => 'Failed to delete cage',
                'error' => true, 
            ];
        }
    }
}
