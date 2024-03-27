<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models\Person;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use app\models\User;

class PersonController extends ActiveController
{
    public $modelClass = 'app\models\Person';

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
     * Menampilkan data diri berdasarkan ID.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException jika data diri tidak ditemukan
     */
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    /**
     * Membuat data diri baru.
     * @return mixed
     * @throws BadRequestHttpException jika input tidak valid
     * @throws ServerErrorHttpException jika data diri tidak dapat disimpan
     */
    public function actionCreatePerson()
    {
        // Check if user already has a person_id
        $userId = Yii::$app->user->identity->id;
        $user = User::findOne($userId);

        if ($user && $user->person_id !== null) {
            throw new BadRequestHttpException('You already have a registered person record');
        }

        // Check if user already has a person record based on bearer token
        $existingPerson = Person::findOne(['user_id' => $userId]);

        if ($existingPerson) {
            throw new BadRequestHttpException('You already have a registered person record');
        }

        // Proceed to create new person
        $model = new Person();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            // Update user's person_id
            if ($user) {
                $user->person_id = $model->id;
                $user->save(false);
            }

            Yii::$app->getResponse()->setStatusCode(201);
            return [
                'message' => 'Person record created successfully',
                'error' => false,
                'data' => $model,
            ];
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason');
        } else {
            throw new BadRequestHttpException('Failed to create the object due to validation error', 422);
        }
    }

    /**
     * Memperbarui data diri berdasarkan ID.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException jika data diri tidak ditemukan
     * @throws BadRequestHttpException jika input tidak valid
     * @throws ServerErrorHttpException jika data diri tidak dapat disimpan
     */
    public function actionUpdatePerson($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            return [
                'message' => 'Person record updated successfully',
                'error' => false,
                'data' => $model,
            ];
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        } else {
            throw new BadRequestHttpException('Failed to update the object due to validation error.', 422);
        }
    }

    /**
     * Menghapus data diri berdasarkan ID.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException jika data diri tidak ditemukan
     * @throws ForbiddenHttpException jika pengguna tidak diizinkan menghapus data diri
     */
    // public function actionDeletePerson($id)
    // {
    //     $model = $this->findModel($id);
        
    //     // Pastikan bahwa data diri yang dihapus terkait dengan pengguna yang sedang login
    //     if ($model->user_id !== Yii::$app->user->id) {
    //         throw new ForbiddenHttpException('You are not allowed to delete this person record.');
    //     }

    //     // Simpan ID pengguna sebelum menghapus data diri
    //     $userId = $model->user_id;

    //     // Hapus hanya data diri
    //     $model->delete();

    //     // Perbarui person_id pada pengguna menjadi null
    //     User::updateAll(['person_id' => null], ['id' => $userId]);

    //     return [
    //         'message' => 'Person record deleted successfully',
    //         'error' => false,
    //     ];
    // }

    /**
     * Mengembalikan semua data diri.
     * @return array
     */
    public function actionIndex()
    {
        return [
            'message' => 'Person records retrieved successfully',
            'error' => false,
            'data' => Person::find()->all(),
        ];
    }

    /**
     * Menemukan model data diri berdasarkan ID.
     * @param integer $id
     * @return Person the loaded model
     * @throws NotFoundHttpException jika data diri tidak ditemukan
     */
    protected function findModel($id)
    {
        if (($model = Person::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested object does not exist');
        }
    }
}
