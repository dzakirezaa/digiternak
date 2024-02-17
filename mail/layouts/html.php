<?php
/** @var \yii\web\View $this view component instance */
/** @var \app\models\User $user the user model */
use yii\helpers\Html;

$this->title = 'Reset Password';

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['user/request-password-reset', 'token' => $user->password_reset_token]);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="password-reset">
        <p>Hello <?= Html::encode($user->username) ?>,</p>
        <?php if (isset($resetLink)) : ?>
            <p>Follow the link below to reset your password:</p>
            <p><?= Html::a('Reset Password', $resetLink) ?></p>
        <?php else : ?>
            <p>We're sorry, but something went wrong. Please contact support for assistance.</p>
        <?php endif; ?>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
