<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\User;

class BaseController extends ActiveController
{
    protected $noAuthActions = [];

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (in_array($action->id, $this->noAuthActions)) {
            return true; // Skip token verification for specified actions
        }

        $token = Yii::$app->request->getHeaders()->get('Authorization');
        if ($token !== null && !User::verifyJwt($token)) {
            throw new \yii\web\UnauthorizedHttpException('Your token is invalid or expired.');
            return false;
        }

        return true; // Proceed with the action since the token is valid
    }

    public function getValidationErrors($model)
    {
        $errorDetails = [];
        foreach ($model->errors as $errors) {
            foreach ($errors as $error) {
                $errorDetails[] = $error;
            }
        }
        return $errorDetails;
    }
}