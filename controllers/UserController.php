<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models\User;
use app\models\UserRole;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\EditProfileForm;
use app\models\RequestPasswordResetForm;
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
                    'message' => 'User logged in successfully',
                    // 'redirect_url' => Yii::$app->urlManager->createAbsoluteUrl(['person/create']),
                    'error' => false,
                    'data' => [
                        'token' => $user->auth_key
                    ]
                ];
            } else {
                // Jika sudah, beri tahu klien bahwa pengguna berhasil login
                Yii::$app->response->statusCode = 200; // OK
                return [
                    'message' => 'User logged in successfully',
                    // 'redirect_url' => Yii::$app->urlManager->createAbsoluteUrl(['site/index']),
                    'error' => false,
                    'data' => [
                        'token' => $user->auth_key
                    ]
                ];
            }
        } else {
            // Jika login gagal
            Yii::$app->response->statusCode = 401; // Unauthorized
            return [
                'message' => 'Invalid username or password',
                'error' => true
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
                'message' => 'User registered successfully', // Tambahkan atribut message
                'error' => false,
                'data' => $result['user'],
            ];
        
            // Redirect ke halaman login
            // Yii::$app->getResponse()->getHeaders()->set('Location', \yii\helpers\Url::to(['user/login'], true));
        
            return $response;
        } else {
            Yii::$app->getResponse()->setStatusCode(500); // Set status code 500 Internal Server Error
            return [
                'message' => 'Failed to register user', // Tambahkan atribut message
                'error' => true,
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
                'message' => 'User not found',
                'error' => true,
            ];
        }

        // Lakukan proses logout
        Yii::$app->user->logout();

        return [
            'message' => 'User logged out successfully',
            'error' => false,
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

    /**
     * Handle retrieving all user profiles.
     *
     * @return array
     */
    public function actionAllProfiles()
    {
        $users = User::find()->all();

        return $users;
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
                        'message' => 'Username is already taken. Please choose a different username',
                        'error' => true,
                    ];
                }
            }

            $user->username = $model->username;

            if ($user instanceof User && $user->save(false)) {
                $userRole = UserRole::findOne($user->role_id);
                Yii::$app->getResponse()->setStatusCode(200); // OK
                return [
                    'message' => 'Profile updated successfully',
                    'error' => false,
                    'data' => $user,
                ];
            } else {
                Yii::$app->getResponse()->setStatusCode(500); // Internal Server Error
                return [
                    'message' => 'Failed to update profile',
                    'error' => true,
                    'details' => $user->errors,
                ];
            }
        } else {
            Yii::$app->getResponse()->setStatusCode(400); // Bad Request
            return [
                'message' => 'Invalid data provided',
                'error' => true,
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
        $model = new RequestPasswordResetForm();
        if ($model->load(Yii::$app->request->getBodyParams(), '') && $model->validate()) {
            if ($model->sendEmail()) { // Periksa apakah email berhasil dikirim
                return ['error' => true];
            } else {
                return [
                    'error' => false,
                    'message' => 'Failed to send password reset email.'
                ];
            }
        }

        Yii::$app->getResponse()->setStatusCode(400); // Bad Request
        return $model;
    }

    // Format user data for response
    // private function formatUser($user)
    // {
    //     $formattedUser = $user->toArray();
    //     $formattedUser['updated_at'] = Yii::$app->formatter->asDatetime($user->updated_at);
    //     return $formattedUser;
    // }
}

