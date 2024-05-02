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

        // Menambahkan authenticator untuk otentikasi
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['register', 'login', 'logout', 'verify-email', 'request-password-reset'],
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
            // If login is successful
            $user = User::findByUsername($model->username);

            // Check if the user's email has been verified
            if ($user->verification_token !== null) {
                // If the user's email has not been verified
                Yii::$app->response->statusCode = 401; // Unauthorized
                return [
                    'message' => 'Please verify your email before logging in',
                    'error' => true
                ];
            }

            // Inform the client that the user has logged in successfully
            Yii::$app->response->statusCode = 200; // OK
            return [
                'message' => 'User logged in successfully',
                'error' => false,
                'data' => [
                    'token' => $user->auth_key,
                    'id' => $user->id,
                ]
            ];
        } else {
            // If login fails
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
    // public function actionRegister()
    // {
    //     $model = new RegisterForm();
    //     $model->load(Yii::$app->request->getBodyParams(), '');

    //     $result = $model->register();

    //     if ($result !== null && isset($result['user'])) {
    //         Yii::$app->getResponse()->setStatusCode(201); // Set status code 201 Created
            
    //         // Menyiapkan pesan JSON
    //         $response = [
    //             'message' => 'User registered successfully', // Tambahkan atribut message
    //             'error' => false,
    //             'data' => $result['user'],
    //         ];
        
    //         // Redirect ke halaman login
    //         // Yii::$app->getResponse()->getHeaders()->set('Location', \yii\helpers\Url::to(['user/login'], true));
        
    //         return $response;
    //     } else {
    //         Yii::$app->getResponse()->setStatusCode(500); // Set status code 500 Internal Server Error
    //         return [
    //             'message' => 'Failed to register user', // Tambahkan atribut message
    //             'error' => true,
    //             'details' => $model->errors,
    //         ];
    //     }
    // }

    public function actionRegister()
    {
        $model = new RegisterForm();
        $model->load(Yii::$app->request->getBodyParams(), '');

        $user = $model->register();

        if ($user instanceof User) {
            Yii::$app->getResponse()->setStatusCode(201); // Set status code 201 Created

            // Generate a verification token
            $verificationToken = Yii::$app->security->generateRandomString();
            // Encode the creation time within the token
            $encodedToken = base64_encode($verificationToken . ':' . time());

            $user->verification_token = $encodedToken;
            $user->save();

            // Send a verification email
            Yii::$app->mailer->compose(['html' => '@app/mail/emailVerify-html', 'text' => '@app/mail/emailVerify-text'], ['user' => $user])
                ->setFrom(['digiternak@gmail.com' => ' Digiternak'])
                ->setTo($user->email)
                ->setSubject('Account registration at Digiternak')
                ->send();

            // Prepare the JSON response
            $userData = $user->toArray(['username', 'email', 'id', 'role']);
            $response = [
                'message' => 'User registered successfully. Please check your email for verification instructions.',
                'error' => false,
                'data' => $userData,
            ];

            return $response;
        } else {
            Yii::$app->getResponse()->setStatusCode(500); // Set status code 500 Internal Server Error
            return [
                'message' => 'Failed to register user',
                'error' => true,
                'details' => $model->errors,
            ];
        }
    }

    /**
     * Handle email verification.
     *
     * @param string $token
     * @return array
     */
    public function actionVerifyEmail($token)
    {
        $user = User::find()->where(['verification_token' => $token])->one();

        if ($user !== null) {
            // Decode the token and get the creation time
            list($verificationToken, $creationTime) = explode(':', base64_decode($token));

            // Check if the token has expired
            if (time() - $creationTime > 24 * 60 * 60) {
                Yii::$app->getResponse()->setStatusCode(400); // Set status code 400 Bad Request
                return [
                    'message' => 'Verification token has expired',
                    'error' => true,
                ];
            }

            $user->verification_token = null;
            $user->save();

            return [
                'message' => 'Email verified successfully',
                'error' => false,
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(400); // Set status code 400 Bad Request
            return [
                'message' => 'Invalid verification token',
                'error' => true,
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
        $user = Yii::$app->user->identity;
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'gender_id' => $user->gender_id,
            'nik' => $user->nik,
            'full_name' => $user->full_name,
            'birthdate' => $user->birthdate,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
            'role' => [
                'id' => $user->role->id,
                'name' => $user->role->name,
            ],
            'is_completed' => (bool)$user->is_completed,
        ];
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

            $user->username = $model->username ?? $user->username;
            $user->nik = $model->nik ?? $user->nik;
            $user->full_name = $model->full_name ?? $user->full_name;
            $user->birthdate = $model->birthdate ?? $user->birthdate;
            $user->phone_number = $model->phone_number ?? $user->phone_number;
            $user->gender_id = $model->gender_id ?? $user->gender_id;
            $user->address = $model->address ?? $user->address;
            
            // Set is_completed to true if all required fields are filled
            if ($user->nik && $user->full_name && $user->birthdate && $user->phone_number && $user->gender_id && $user->address) {
                $user->is_completed = 1;
            }

            if ($user instanceof User && $user->save(false)) {
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
}

