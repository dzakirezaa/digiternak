<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
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
 * @property string $password write-only password
 * @property integer $person_id
 * @property integer $role_id
 * @property boolean $is_deleted
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
                'value' => new Expression('NOW()'),
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
            [['status', 'role_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['email', 'auth_key', 'password_hash', 'password_reset_token', 'verification_token'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 50],
            [['password_reset_token', 'email', 'username'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserRole::class, 'targetAttribute' => ['role_id' => 'id']],
            [['is_completed', 'is_verified'], 'default', 'value' => 0],
            [['is_completed', 'is_verified'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields = parent::fields();

        // Menambahkan fields yang ingin disertakan dalam response JSON
        $fields['role'] = function () {
            $userRole = UserRole::findOne($this->role_id);
            return [
                'id' => $userRole->id,
                'name' => $userRole->name,
            ];
        };

        $fields['is_completed'] = function () {
            return $this->is_completed == 1 ? true : false;
        };

        $fields['is_verified'] = function () {
            return $this->is_verified == 1 ? true : false;
        };

        // Menghapus fields yang tidak perlu disertakan dalam response JSON
        unset($fields['password_hash'], $fields['password_reset_token'], $fields['verification_token'], $fields['auth_key'], $fields['status'], $fields['created_at'], $fields['updated_at'], $fields['role_id']);

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
     * Generates authentication key
     */
    public function generateAuthKey()
    {
        $timestamp = time();
        $userId = $this->id; // assuming the user model has an 'id' attribute
        $username = $this->username; // assuming the user model has a 'username' attribute
        $secretKey = Yii::$app->params['secretKey']; // a secret key defined in your application parameters

        // Generate a token with the format 'hash(userId-username-timestamp-randomString-secretKey)'
        $this->auth_key = hash('sha256', $userId . '-' . $username . '-' . $timestamp . '-' . Yii::$app->security->generateRandomString() . '-' . $secretKey);
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

    /**
     * Gets role associated with the user.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(UserRole::class, ['id' => 'role_id']);
    }
}
