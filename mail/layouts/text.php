<?php

/** 
 * @var yii\web\View $this view component instance
 * @var yii\mail\BaseMessage $message the message being composed
 * @var string $content main view render result
 * @var \app\models\User $user the user model
 */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['user/request-password-reset', 'token' => $user->password_reset_token]);
?>

Hello <?= $user->username ?>,

Follow the link below to reset your password:

<?= $resetLink ?>
