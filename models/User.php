<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'required'],
            [['status', 'is_completed'], 'integer'],
            [['created_at', 'updated_at', 'birthdate'], 'safe'],
            [['email', 'auth_key', 'password_hash', 'password_reset_token', 'verification_token', 'gender', 'nik', 'full_name', 'phone_number', 'address'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 50],
            [['password_reset_token', 'email', 'username', 'nik'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            ['is_completed', 'default', 'value' => 0],
            ['is_completed', 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields = parent::fields();

        $fields['is_completed'] = function () {
            return $this->is_completed == 1 ? true : false;
        };

        // Menghapus fields yang tidak perlu disertakan dalam response JSON
        unset($fields['password_hash'], $fields['password_reset_token'], $fields['verification_token'], $fields['auth_key'], $fields['status']);

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates JWT
     */
    public function generateJwt()
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $timestamp = time();
        $expireTime = $timestamp + 3600; 
        $userId = $this->id; // assuming the user model has an 'id' attribute
        $username = $this->username; // assuming the user model has a 'username' attribute
        $payload = json_encode([
            'userId' => $userId, 
            'username' => $username, 
            'iat' => $timestamp,
            'exp' => $expireTime // Expiration time
        ]);
        
        $secretKey = Yii::$app->params['secretKey']; // a secret key defined in your application parameters
        
        // Base64Url encode the header and payload
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        // Create the signature hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secretKey, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        // Concatenate Header, Payload, and Signature to get the JWT
        $this->auth_key = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Verifies JWT
     */
    public static function verifyJwt($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false; // Invalid token
        }

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
        $currentTime = time();

        if (!isset($payload['exp']) || $currentTime >= $payload['exp']) {
            return false; // Token is expired or invalid
        }

        // Optionally, verify the signature here for extra security

        return true; // Token is valid
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new password reset token and sends email to user
     *
     * @return bool whether the token is generated and email is sent successfully
     */
    public function sendPasswordResetEmail()
    {
        $this->generatePasswordResetToken();
        if ($this->save(false)) {
            return Yii::$app->mailer->compose(['html' => 'html', 'text' => 'text'], ['user' => $this])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                ->setTo($this->email)
                ->setSubject('Password reset for ' . Yii::$app->name)
                ->send();
        }
        return false;
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function findByUsernameOrEmail($username, $email)
    {
        // Find a user by username or email
        $user = User::find()
            ->where(['username' => $username])
            ->orWhere(['email' => $email])
            ->one();

        return $user;
    }
}