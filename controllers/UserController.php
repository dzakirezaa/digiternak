<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models\User;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\EditProfileForm;
use yii\web\BadRequestHttpException;

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
            'except' => ['register', 'logout'], // Tambahkan action yang tidak memerlukan otentikasi di sini
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

        // Cek apakah request memiliki access token
        $accessToken = Yii::$app->request->getHeaders()->get('Authorization');
        if (!$accessToken) {
            Yii::$app->session->setFlash('error', 'Access token is required.');
            return $this->redirect(['user/login']); // Redirect ke halaman login
        }

        if ($model->login()) {
            // Jika login berhasil
            $user = Yii::$app->user->identity;
            // Cek apakah user telah mengisi data diri
            if ($user->person_id === null) {
                // Jika belum, arahkan ke halaman untuk mengisi data diri
                return $this->redirect(['person/create']);
            } else {
                // Jika sudah, arahkan ke halaman utama
                return $this->redirect(['site/index']);
            }
        } else {
            // Jika login gagal, tampilkan pesan kesalahan pada halaman login
            Yii::$app->session->setFlash('error', 'Login failed.');
            return $this->render('login', [
                'model' => $model,
            ]);
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
            Yii::$app->getResponse()->setStatusCode(201); // Set status code 201 Created
            
            // Menyiapkan pesan JSON
            $response = [
                'name' => 'Registration Success', // Contoh atribut 'name'
                'message' => 'User registered successfully.', // Tambahkan atribut message
                'user' => $result['user'],
                'access_token' => $result['token'],
            ];
        
            // Redirect ke halaman login
            Yii::$app->getResponse()->getHeaders()->set('Location', \yii\helpers\Url::to(['user/login'], true));
        
            return $response;
        } else {
            Yii::$app->getResponse()->setStatusCode(500); // Set status code 500 Internal Server Error
            return [
                'name' => 'Registration Failed',
                'message' => 'Failed to register user.', // Tambahkan atribut message
                'error' => 'Failed to register user.',
                'details' => $model->errors,
            ];
        }
    }

    /**
     * Handle user logout.
     *
     * @return array
     */
    public function actionLogout()
    {
        $user = Yii::$app->user->identity;

        // Periksa apakah pengguna yang sedang login ada
        if (!$user) {
            return [
                'name' => 'Logout Failed',
                'message' => 'User not found.',
                'error' => 'User not found.',
            ];
        }

        // Lakukan proses logout
        Yii::$app->user->logout();

        return [
            'name' => 'Logout Success',
            'message' => 'User logged out successfully.',
        ];
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
                // tambahkan properti lain yang ingin ditampilkan
            ];
        }

        return $profiles;
    }

    /**
     * Handle editing user profile.
     *
     * @return array|BadRequestHttpException
     */
    public function actionEditProfile()
    {
        $user = Yii::$app->user->identity;

        // Periksa apakah pengguna yang sedang login ada
        if (!$user) {
            throw new BadRequestHttpException('User not found.');
        }

        $model = new EditProfileForm();

        // Memuat data dari permintaan dengan melewati parameter kedua kosong, sehingga akan memuat semua atribut
        $model->load(Yii::$app->request->getBodyParams(), '');

        // Validasi dan simpan perubahan profil
        if ($model->editProfile($user)) {
            return [
                'name' => 'Profile Updated',
                'message' => 'Profile updated successfully.',
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(400); // Set status code 400 Bad Request
            return [
                'name' => 'Profile Update Failed',
                'message' => 'Failed to update profile.',
                'error' => 'Failed to update profile.',
                'details' => $model->errors,
            ];
        }
    }

    /**
     * Handle request to reset password via email
     *
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionRequestPasswordReset()
    {
        $email = Yii::$app->request->getBodyParam('email');
        if (!$email) {
            throw new BadRequestHttpException('Email is required.');
        }
        $user = User::findOne(['email' => $email]);
        if (!$user) {
            throw new BadRequestHttpException('User not found.');
        }
        if ($user->sendPasswordResetEmail()) {
            return ['message' => 'Password reset email has been sent. Please check your email inbox.'];
        } else {
            throw new BadRequestHttpException('Failed to send password reset email.');
        }
    }
}
