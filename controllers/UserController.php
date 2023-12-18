<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models\User;
use app\models\LoginForm;
use app\models\RegisterForm;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Menambahkan authenticator untuk otentikasi menggunakan access token
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['login', 'register'], // Tambahkan action yang tidak memerlukan otentikasi di sini
        ];

        return $behaviors;
    }

    /**
     * Handle user login.
     *
     * @return array|LoginForm
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->getBodyParams(), '');

        if ($model->login()) {
            return [
                'access_token' => Yii::$app->user->identity->access_token,
            ];
        } else {
            return $model;
        }
    }

    /**
     * Handle user registration.
     *
     * @return array|RegisterForm
     */
    public function actionRegister()
    {
        $model = new RegisterForm();
        $model->load(Yii::$app->request->getBodyParams(), '');

        $result = $model->register();
        if ($result !== null && isset($result['user'])) {
            return [
                'user' => $result['user'],
                'access_token' => $result['token'],
            ];
        } else {
            return [
                'error' => 'Failed to register user.',
                'details' => $model->errors,
            ];
        }
    }
    
    /**
     * Handle retrieving user data.
     *
     * @return User
     */
    public function actionProfile()
    {
        return Yii::$app->user->identity;
    }

    // Aksi untuk mendapatkan seluruh profil pengguna
    public function actionAllProfiles()
    {
        $users = User::find()->all();
        $profiles = [];

        foreach ($users as $user) {
            $profiles[] = [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'created_at' => Yii::$app->formatter->asDatetime($user->created_at),
                'updated_at' => Yii::$app->formatter->asDatetime($user->updated_at),
                // ... tambahkan properti lain yang ingin Anda tampilkan
            ];
        }

        return $profiles;
    }
}
