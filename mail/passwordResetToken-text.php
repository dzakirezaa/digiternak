<?php

/* @var yii\web\View $this view component instance */
/* @var app\models\User $user the user object */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['user/request-password-reset', 'token' => $user->password_reset_token]);
?>

Hello <?= $user->username ?>,

You have requested to reset your password. Please click the link below to proceed:

<?= $resetLink ?>

If you did not request a password reset, please ignore this email.

Thank you,
The Digiternak Team
