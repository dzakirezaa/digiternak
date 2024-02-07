<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models\Person;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
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
    public function actionCreate()
    {
        $model = new Person();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            // Update user's person_id if the user does not have a person_id yet
            $userId = Yii::$app->user->identity->id;
            $user = User::findOne($userId);

            if ($user) {
                if ($user->person_id === null) {
                    // If the user does not have a person_id yet, update it
                    $user->person_id = $model->id;
                    $user->save(false); // Save without revalidating
                } else {
                    // If the user already has a person_id, skip
                    Yii::$app->getResponse()->setStatusCode(201);
                    return [
                        'status' => 'success',
                        'message' => 'Data diri berhasil dibuat.',
                        'data' => $model,
                    ];
                }
            } else {
                // Handle the case where the user is not found
                throw new NotFoundHttpException('User not found.');
            }

            Yii::$app->getResponse()->setStatusCode(201);
            return [
                'status' => 'success',
                'message' => 'Data diri berhasil dibuat.',
                'data' => $model,
            ];
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        } else {
            throw new BadRequestHttpException('Failed to create the object due to validation error.', 422);
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
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            return [
                'status' => 'success',
                'message' => 'Data diri berhasil diperbarui.',
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
     * @throws NotFoundHttpException jika data diri tidak ditemukan
     * @throws ServerErrorHttpException jika data diri tidak dapat dihapus
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return [
            'status' => 'success',
            'message' => 'Data diri berhasil dihapus.',
        ];
    }

    /**
     * Mengembalikan semua data diri.
     * @return array
     */
    public function actionIndex()
    {
        return [
            'status' => 'success',
            'message' => 'Menampilkan semua data diri.',
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
            throw new NotFoundHttpException('The requested object does not exist.');
        }
    }
}
