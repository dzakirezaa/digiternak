<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['user/request-password-reset', 'token' => $user->password_reset_token]);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body>
    <h1>Password Reset</h1>
    <p>Hello <?= Html::encode($user->username) ?>,</p>
    <p>You have requested to reset your password. Please click the link below to proceed:</p>
    <p><?= Html::a('Reset Password', $resetLink) ?></p>
    <p>If you did not request a password reset, please ignore this email.</p>
    <p>Thank you,</p>
    <p>The Digiternak Team</p>
</body>
</html>
