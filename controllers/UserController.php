<?php

namespace app\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use app\models\User;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\EditProfileForm;
use app\models\RequestPasswordResetForm;
use yii\web\BadRequestHttpException;

class UserController extends BaseController
{
    public $modelClass = 'app\models\User';

    public function init()
    {
        parent::init();
        $this->noAuthActions = ['register', 'login', 'verify-email', 'request-password-reset'];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // authenticator untuk otentikasi
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['register', 'login', 'verify-email', 'request-password-reset'],
        ];

        // VerbFilter untuk memastikan setiap action hanya menerima HTTP method yang sesuai
        $behaviors['verbs'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'register' => ['POST'],
                'login' => ['POST'],
                'logout' => ['POST'],
                'verify-email' => ['GET'],
                'request-password-reset' => ['POST'],
                'profile' => ['GET'],
                'edit-profile' => ['PUT'],
            ],
        ];

        return $behaviors;
    }

    /**
     * Handle user registration.
     *
     * @return array
     */
    public function actionRegister()
    {
        $model = new RegisterForm();
        $model->load(Yii::$app->request->getBodyParams(), '');

        if (!$model->validate()) {
            $errors = $model->getErrors();
            if ($this->isUsernameOrEmailTaken($errors)) {
                Yii::$app->response->setStatusCode(409); // Conflict
                return [
                    'message' => 'Email atau username sudah digunakan oleh pengguna lain.',
                    'error' => true,
                    'details' => $this->getValidationErrors($model),
                ];
            } else {
                Yii::$app->response->setStatusCode(400); // Bad Request
                return [
                    'message' => 'Atribut ada yang kosong atau format tidak valid.',
                    'error' => true,
                    'details' => $this->getValidationErrors($model),
                ];
            }
        }

        // Generate a verification token that includes the user's data
        $userData = [
            'username' => $model->username,
            'email' => $model->email,
            'password_hash' => Yii::$app->security->generatePasswordHash($model->password),
        ];
        $tokenData = base64_encode(json_encode($userData));
        $model->verification_token = $tokenData;

        $user = $model->register();

        if ($user instanceof User) {
            $user->verification_token = $tokenData;

            $userData = $user->toArray(['id', 'username', 'email']);

            try {
                Yii::$app->mailer->compose(['html' => '@app/mail/emailVerify-html', 'text' => '@app/mail/emailVerify-text'], ['user' => $user])
                    ->setFrom(['digiternak@gmail.com' => ' Digiternak'])
                    ->setTo($user->email)
                    ->setSubject('Account registration at Digiternak')
                    ->send();

                Yii::$app->response->setStatusCode(201); // Created
                return [
                    'message' => 'Akun berhasil dibuat. Silakan cek email Anda untuk verifikasi.',
                    'error' => false,
                    'data' => $userData,
                ];
            } catch (\Exception $e) {
                Yii::$app->response->setStatusCode(500); // Internal Server Error
                return [
                    'message' => 'Gagal mengirim email verifikasi.',
                    'error' => true,
                    'details' => $e->getMessage(),
                ];
            }
        } else {
            Yii::$app->response->setStatusCode(400); // Bad Request
            return [
                'message' => 'Atribut ada yang kosong atau format tidak valid.',
                'error' => true,
                'details' => $this->getValidationErrors($model),
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
        try {
            // Decode the token and get the user's data
            $userData = json_decode(base64_decode($token), true);
            if ($userData === null) {
                throw new \Exception('Token verifikasi tidak valid.');
            }
        } 
        catch (\Exception $e) {
            Yii::$app->response->setStatusCode(400);
            return [
                'message' => $e->getMessage(),
                'error' => true,
            ];
        }

        // Find the user by username
        $user = User::findOne(['username' => $userData['username']]);
        if ($user === null) {
            Yii::$app->response->setStatusCode(400);
            return [
                'message' => 'User tidak ditemukan.',
                'error' => true,
            ];
        }

        // Verify the email
        $user->status = User::STATUS_ACTIVE;
        $user->verification_token = null;
       
        if (!$user->save()) {
            Yii::$app->response->setStatusCode(500);
            return [
                'message' => 'Gagal memverifikasi email.',
                'error' => true,
            ];
        }

        Yii::$app->response->setStatusCode(200);
        return [
            'message' => 'Email berhasil diverifikasi',
            'error' => false,
        ];
    }

    /**
     * Handle user login.
     *
     * @return array|LoginForm
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $params = Yii::$app->request->getBodyParams();
        $model->load($params, '');

        // Check if the required fields are present
        if (!isset($params['username']) || !isset($params['password'])) {
            Yii::$app->response->statusCode = 400; // Bad Request
            return [
                'message' => 'Field username dan password harus diisi.',
                'error' => true
            ];
        }

        if ($model->login()) {
            // If login is successful
            $user = User::findByUsername($model->username);

            // Check if the user's email has been verified
            if ($user->verification_token !== null) {
                // If the user's email has not been verified
                Yii::$app->response->statusCode = 401; // Unauthorized
                return [
                    'message' => 'Email belum diverifikasi. Silakan cek email Anda untuk instruksi verifikasi.',
                    'error' => true
                ];
            }

            // Inform the client that the user has logged in successfully
            Yii::$app->response->statusCode = 200; // OK
            return [
                'message' => 'Pengguna berhasil login.',
                'error' => false,
                'data' => [
                    'token' => $user->auth_key,
                    'id' => $user->id,
                ]
            ];
        } 
            else {
            // If login fails
            Yii::$app->response->statusCode = 401; // Unauthorized
            return [
                'message' => 'Username atau password salah. Silakan coba lagi.',
                'error' => true
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

        // Check if the currently logged-in user exists
        if (!$user) {
            return [
                'message' => 'Pengguna tidak ditemukan',
                'error' => true,
            ];
        }

        // Invalidate the token
        $user->auth_key = null;
        if ($user !== null) {
            User::updateAll(['auth_key' => null], 'id = :id', [':id' => $user->id]);
        }

        // Perform the logout process
        Yii::$app->user->logout();

        return [
            'message' => 'Pengguna berhasil logout',
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
        try {
            if (Yii::$app->user->isGuest) {
                throw new \yii\web\UnauthorizedHttpException();
            }
    
            $user = Yii::$app->user->identity;
    
            return [
                'message' => 'Profil pengguna berhasil ditemukan',
                'error' => false,
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'nik' => $user->nik,
                    'full_name' => $user->full_name,
                    'birthdate' => $user->birthdate,
                    'phone_number' => $user->phone_number,
                    'address' => $user->address,
                    'is_completed' => (bool)$user->is_completed,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
            ];
        } catch (\yii\web\UnauthorizedHttpException $e) {
            Yii::$app->response->statusCode = 401;
            return [
                'message' => 'Token Invalid.',
                'error' => false,
            ];
        }
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
            $user->gender = $model->gender ?? $user->gender;
            $user->address = $model->address ?? $user->address;
            
            // Set is_completed to true if all required fields are filled
            if ($user->nik && $user->full_name && $user->birthdate && $user->phone_number && $user->gender && $user->address) {
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
                return [
                    'message' => 'Gagal memperbarui profil',
                    'error' => true,
                    'details' => $this->getValidationErrors($model),
                ];
            }
        } else {
            Yii::$app->getResponse()->setStatusCode(400); // Bad Request
            return [
                'message' => 'Data yang diberikan tidak valid',
                'error' => true,
                'details' => $this->getValidationErrors($model),
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

    /**
     * Check if the username or email is already taken.
     *
     * @param array $errors
     * @return bool
     */
    private function isUsernameOrEmailTaken($errors) {
        $usernameErrors = isset($errors['username']) ? $errors['username'] : [];
        $emailErrors = isset($errors['email']) ? $errors['email'] : [];
    
        foreach ($usernameErrors as $error) {
            if (strpos($error, 'sudah digunakan') !== false) {
                return true;
            }
        }
    
        foreach ($emailErrors as $error) {
            if (strpos($error, 'sudah digunakan') !== false) {
                return true;
            }
        }
    
        return false;
    }
}