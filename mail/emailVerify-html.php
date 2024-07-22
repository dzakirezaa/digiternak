<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user app\models\User */

$this->params['user'] = $user;

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['user/verify-email', 'token' => $user->verification_token]);
?>
<html>
<body>
    <div style="text-align: left; font-size: 18px; font-weight: normal;">
        <img src="https://storage.googleapis.com/digiternak1/digdaya%20-%20100x100.png" alt="Logo" style="width: 200px; height: auto;">
        <p>Hi <?= Html::encode($user->username) ?>,</p>
        <p>We're happy you signed up for Digiternak. To start exploring please confirm your email address.</p>
        <div style="text-align: center;">
            <a href="<?= Html::encode($verifyLink) ?>" style="background-color: #ebab34; color: white; padding: 14px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border: none; border-radius: 12px;">Verify Now</a>
        </div>
        <p>Welcome to Digiternak!</p>
        <p>Digiternak Team</p>
    </div>
</body>
</html>