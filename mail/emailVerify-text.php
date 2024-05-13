<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user app\models\User */

$this->params['user'] = $user;

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['user/verify-email', 'token' => $user->verification_token]);
?>

Hi <?= Html::encode($user->username) ?>,

We're happy you signed up for Digiternak. To start exploring please confirm your email address.

Verify Now: <?= Html::encode($verifyLink) ?>

Welcome to Digiternak!

Digiternak Team

This verification link will expire in 24 hours.