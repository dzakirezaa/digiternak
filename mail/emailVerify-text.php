<?php
/* @var $this yii\web\View */
/* @var $user app\models\User */

$this->params['user'] = $user;

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['user/verify-email', 'token' => $user->verification_token]);
?>
Hello <?= $user->username ?>,

Follow the link below to verify your email:

<?= $verifyLink ?>