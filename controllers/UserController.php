<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models\User;
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
            'except' => ['register', 'login', 'logout', 'verify-email', 'request-password-reset', 'verify-user'],
        ];

        return $behaviors;
    }

    /**
     * Handle user login.
     *
     * @return array|LoginForm
     */
    // public function actionLogin()
    // {
    //     $model = new LoginForm();
    //     $model->load(Yii::$app->request->getBodyParams(), '');

    //     if ($model->login()) {
    //         // If login is successful
    //         $user = User::findByUsername($model->username);

    //         // Check if the user's email has been verified
    //         if ($user->verification_token !== null) {
    //             // If the user's email has not been verified
    //             Yii::$app->response->statusCode = 401; // Unauthorized
    //             return [
    //                 'message' => 'Please verify your email before logging in',
    //                 'error' => true
    //             ];
    //         }

    //         // Inform the client that the user has logged in successfully
    //         Yii::$app->response->statusCode = 200; // OK
    //         return [
    //             'message' => 'User logged in successfully',
    //             'error' => false,
    //             'data' => [
    //                 'token' => $user->auth_key,
    //                 'id' => $user->id,
    //             ]
    //         ];
    //     } else {
    //         // If login fails
    //         Yii::$app->response->statusCode = 401; // Unauthorized
    //         return [
    //             'message' => 'Invalid username or password',
    //             'error' => true
    //         ];
    //     }
    // }

    // This function actionRegister is use for when user want to register and should verify their email first.
    // public function actionRegister()
    // {
    //     $model = new RegisterForm();
    //     $model->load(Yii::$app->request->getBodyParams(), '');

    //     $user = $model->register();

    //     if ($user instanceof User) {
    //         Yii::$app->getResponse()->setStatusCode(201); // Set status code 201 Created

    //         // Generate a verification token
    //         $verificationToken = Yii::$app->security->generateRandomString();
    //         // Encode the creation time within the token
    //         $encodedToken = base64_encode($verificationToken . ':' . time());

    //         $user->verification_token = $encodedToken;
    //         $user->save();

    //         // Send a verification email
    //         Yii::$app->mailer->compose(['html' => '@app/mail/emailVerify-html', 'text' => '@app/mail/emailVerify-text'], ['user' => $user])
    //             ->setFrom(['digiternak@gmail.com' => ' Digiternak'])
    //             ->setTo($user->email)
    //             ->setSubject('Account registration at Digiternak')
    //             ->send();

    //         // Prepare the JSON response
    //         $userData = $user->toArray(['username', 'email', 'id', 'role']);
    //         $response = [
    //             'message' => 'User registered successfully. Please check your email for verification instructions.',
    //             'error' => false,
    //             'data' => $userData,
    //         ];

    //         return $response;
    //     } else {
    //         Yii::$app->getResponse()->setStatusCode(500); // Set status code 500 Internal Server Error
    //         return [
    //             'message' => 'Failed to register user',
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

            // Set is_verified to false by default for non-admin users
            $user->is_verified = $user->role_id == 2 ? true : false;
            $user->save();

            // Prepare the JSON response
            $userData = $user->toArray(['username', 'email', 'id', 'role']);
            $message = $user->role_id == 2 ? 'Akun berhasil dibuat.' : 'Akun berhasil dibuat. Harap tunggu verifikasi dari admin.';
            $response = [
                'message' => $message,
                'error' => false,
                'data' => $userData,
            ];

            return $response;
        } else {
            Yii::$app->getResponse()->setStatusCode(500); // Set status code 500 Internal Server Error
            $errorDetails = [];
            foreach ($model->errors as $errors) {
                foreach ($errors as $error) {
                    $errorDetails[] = $error;
                }
            }
            return [
                'message' => 'Gagal membuat akun',
                'error' => true,
                'details' => $errorDetails,
            ];
        }
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->getBodyParams(), '');

        if ($model->login()) {
            // If login is successful
            $user = User::findByUsername($model->username);

            // Check if the user has been verified by the admin
            if (!$user->is_verified) {
                // If the user has not been verified
                Yii::$app->response->statusCode = 401; // Unauthorized
                return [
                    'message' => 'Akun belum diverifikasi oleh admin. Harap tunggu verifikasi dari admin.',
                    'error' => true
                ];
            }

            // Inform the client that the user has logged in successfully
            Yii::$app->response->statusCode = 200; // OK
            return [
                'message' => 'Pengguna berhasil login',
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
                'message' => 'Username atau password salah',
                'error' => true
            ];
        }
    }

    public function actionVerifyUser($username)
    {
        // Get the auth_key from the request headers
        $authKey = Yii::$app->request->headers->get('Authorization');

        // The auth_key might be prefixed with 'Bearer ' if it's a bearer token
        if (substr($authKey, 0, 7) === 'Bearer ') {
            $authKey = substr($authKey, 7);
        }

        // Find the current user using the auth_key and check if they are an admin
        $currentUser = User::find()->where(['auth_key' => $authKey, 'role_id' => 2])->one();

        // Check if a user is found and they are an admin
        if ($currentUser === null) {
            Yii::$app->response->statusCode = 403; // Forbidden
            return [
                'message' => 'Hanya admin yang dapat memverifikasi pengguna.',
                'error' => true
            ];
        }

        // Find the user to be verified by username
        $userToVerify = User::find()->where(['username' => $username])->one();

        // Check if the user to be verified exists
        if ($userToVerify === null) {
            Yii::$app->response->statusCode = 404; // Not Found
            return [
                'message' => 'Pengguna tidak ditemukan.',
                'error' => true
            ];
        }

        // Check if the user is already verified
        if ($userToVerify->is_verified == 1) {
            return [
                'message' => 'Akun sudah pernah diverifikasi.',
                'error' => false
            ];
        }

        // Verify the user
        $userToVerify->is_verified = 1;
        $userToVerify->save();

        // Return a success message
        return [
            'message' => 'Akun berhasil diverifikasi.',
            'error' => false
        ];
    }

    /**
     * Handle email verification.
     *
     * @param string $token
     * @return array
     */
    // public function actionVerifyEmail($token)
    // {
    //     $user = User::find()->where(['verification_token' => $token])->one();

    //     if ($user !== null) {
    //         // Decode the token and get the creation time
    //         list($verificationToken, $creationTime) = explode(':', base64_decode($token));

    //         // Check if the token has expired
    //         if (time() - $creationTime > 24 * 60 * 60) {
    //             Yii::$app->getResponse()->setStatusCode(400); // Set status code 400 Bad Request
    //             return [
    //                 'message' => 'Verification token has expired',
    //                 'error' => true,
    //             ];
    //         }

    //         $user->verification_token = null;
    //         $user->save();

    //         return [
    //             'message' => 'Email verified successfully',
    //             'error' => false,
    //         ];
    //     } else {
    //         Yii::$app->getResponse()->setStatusCode(400); // Set status code 400 Bad Request
    //         return [
    //             'message' => 'Invalid verification token',
    //             'error' => true,
    //         ];
    //     }
    // }

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
                'message' => 'Pengguna tidak ditemukan',
                'error' => true,
            ];
        }

        // Lakukan proses logout
        Yii::$app->user->logout();

        return [
            'message' => 'Penngguna berhasil logout',
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
        if (Yii::$app->user->isGuest) {
            return [
                'message' => 'Anda tidak terotentikasi',
                'error' => true,
            ];
        }

        $user = Yii::$app->user->identity;

        return [
            'message' => 'Profil pengguna berhasil ditemukan',
            'error' => false,
            'data' => [
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
                'is_verified' => (bool)$user->is_verified,
            ],
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
                        'message' => 'Username sudah digunakan oleh pengguna lain. Silakan gunakan username lain.',
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
                    'message' => 'Profil berhasil diperbarui',
                    'error' => false,
                    'data' => $user,
                ];
            } else {
                Yii::$app->getResponse()->setStatusCode(500); // Internal Server Error
                $errorDetails = [];
                foreach ($user->errors as $errors) {
                    foreach ($errors as $error) {
                        $errorDetails[] = $error;
                    }
                }
                return [
                    'message' => 'Gagal memperbarui profil',
                    'error' => true,
                    'details' => $errorDetails,
                ];
            }
        } else {
            Yii::$app->getResponse()->setStatusCode(400); // Bad Request
            $errorDetails = [];
            foreach ($model->errors as $errors) {
                foreach ($errors as $error) {
                    $errorDetails[] = $error;
                }
            }
            return [
                'message' => 'Data yang diberikan tidak valid',
                'error' => true,
                'details' => $errorDetails,
            ];
        }
    }

    /**
     * Handle request to reset password via email
     *
     * @return array|string
     * @throws BadRequestHttpException
     */
    // public function actionRequestPasswordReset()
    // {
    //     $model = new RequestPasswordResetForm();
    //     if ($model->load(Yii::$app->request->getBodyParams(), '') && $model->validate()) {
    //         if ($model->sendEmail()) { // Periksa apakah email berhasil dikirim
    //             return ['error' => true];
    //         } else {
    //             return [
    //                 'error' => false,
    //                 'message' => 'Failed to send password reset email.'
    //             ];
    //         }
    //     }

    //     Yii::$app->getResponse()->setStatusCode(400); // Bad Request
    //     return $model;
    // }
}

