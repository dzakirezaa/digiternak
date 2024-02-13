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
            'except' => ['register', 'login', 'logout'], // Tambahkan action yang tidak memerlukan otentikasi di sini
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
            // Jika login berhasil
            $user = User::findByUsername($model->username);

            // Cek apakah user telah mengisi data diri
            if ($user->person_id === null) {
                // Jika belum, beri tahu klien bahwa pengguna perlu mengisi data diri
                Yii::$app->response->statusCode = 201; // Created
                return [
                    'name' => 'Login Success',
                    'message' => 'Please complete your profile.',
                    'redirect_url' => Yii::$app->urlManager->createAbsoluteUrl(['person/create']),
                    'auth_key' => $user->auth_key
                ];
            } else {
                // Jika sudah, beri tahu klien bahwa pengguna berhasil login
                Yii::$app->response->statusCode = 200; // OK
                return [
                    'name' => 'Login Success',
                    'message' => 'User logged in successfully.',
                    'redirect_url' => Yii::$app->urlManager->createAbsoluteUrl(['site/index']),
                    'auth_key' => $user->auth_key
                ];
            }
        } else {
            // Jika login gagal
            Yii::$app->response->statusCode = 401; // Unauthorized
            return [
                'name' => 'Login Failed',
                'message' => 'Invalid username or password.'
            ];
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
        $model = new EditProfileForm();
        $model->load(Yii::$app->request->getBodyParams(), '');

        if ($model->validate()) {
            if ($model->username !== $user->username) {
                // Check if the new username is unique
                $existingUser = User::findOne(['username' => $model->username]);
                if ($existingUser !== null) {
                    Yii::$app->getResponse()->setStatusCode(400); // Bad Request
                    return [
                        'name' => 'Edit Profile Failed',
                        'message' => 'Username is already taken. Please choose a different username.',
                    ];
                }
            }

            $user->username = $model->username;

            if ($user instanceof User && $user->save()) {
                Yii::$app->getResponse()->setStatusCode(200); // OK
                return [
                    'name' => 'Edit Profile Success',
                    'message' => 'Profile updated successfully.',
                    'user' => $user,
                ];
            } else {
                Yii::$app->getResponse()->setStatusCode(500); // Internal Server Error
                return [
                    'name' => 'Edit Profile Failed',
                    'message' => 'Failed to update profile.',
                    'error' => 'Failed to update profile.',
                    'details' => $user->errors,
                ];
            }
        } else {
            Yii::$app->getResponse()->setStatusCode(400); // Bad Request
            return [
                'name' => 'Edit Profile Failed',
                'message' => 'Invalid data provided.',
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
