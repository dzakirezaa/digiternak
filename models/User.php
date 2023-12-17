<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\UserRoleBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use sizeg\jwt\Jwt;

class User extends ActiveRecord implements IdentityInterface, Jwt
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
            TimestampBehavior::class,
            UserRoleBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['person_id', 'username', 'auth_key', 'password_hash', 'role_id', 'token_jwt'], 'required'],
            [['person_id', 'status', 'role_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'verification_token'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 255],
            [['password_reset_token'], 'unique'],
            [['email'], 'unique'],
            [['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::class, 'targetAttribute' => ['person_id' => 'id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserRole::class, 'targetAttribute' => ['role_id' => 'id']],
            [['token_jwt'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields = parent::fields();

        // Tambahkan kolom token_jwt ke fields
        $fields['token_jwt'] = 'token_jwt';

        // Menambahkan fields dari relasi dengan model Person dan Role
        $fields['person'] = 'person';
        $fields['role'] = 'role';

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
        $jwt = new Jwt();

        try {
            $decodedToken = $jwt->getParser()->parse((string) $token);
        } catch (\Exception $e) {
            return null;
        }

        // Ganti sesuai field ID dari model User
        return static::findOne(['id' => $decodedToken->getClaim('uid'), 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
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
     * {@inheritdoc}
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * {@inheritdoc}
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * {@inheritdoc}
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * {@inheritdoc}
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ])->andWhere(['>', 'created_at', time() - $expire])->one();
    }

    /**
     * {@inheritdoc}
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * {@inheritdoc}
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne(['verification_token' => $token, 'status' => self::STATUS_INACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordResetTokenValid($token)
    {
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);

        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getJwtKey()
    {
        return $_ENV['JWT_SECRET_KEY']; // Ganti dengan env variable yang sesuai
    }

    /**
     * {@inheritdoc}
     */
    public function generateJwtToken($duration = 3600)
    {
        $jwt = new Jwt();

        return $jwt->getBuilder()
            ->setIssuer(Yii::$app->params['jwtIssuer'])
            ->setIssuedAt(time())
            ->setExpiration(time() + $duration)
            ->set('uid', $this->id) // Ganti sesuai field ID dari model User
            ->getToken();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByJwtToken($token)
    {
        $jwt = new Jwt();

        try {
            $decodedToken = $jwt->getParser()->parse((string) $token);
        } catch (\Exception $e) {
            return null;
        }

        // Ganti sesuai field ID dari model User
        return static::findOne(['id' => $decodedToken->getClaim('uid'), 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Gets person associated with the user.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(Person::class, ['id' => 'person_id']);
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
