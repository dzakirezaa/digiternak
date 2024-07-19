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

        // Skip token verification for specified actions
        if (in_array($action->id, $this->noAuthActions)) {
            return true; 
        }

        $token = Yii::$app->request->getHeaders()->get('Authorization');
        if ($token !== null && !User::verifyJwt($token)) {
            throw new \yii\web\UnauthorizedHttpException('Your token is invalid or expired.');
            return false;
        }

        return true;
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

    // public function beforeAction($action)
    // {
    //     if (!parent::beforeAction($action)) {
    //         return false;
    //     }

    //     if (in_array($action->id, $this->noAuthActions)) {
    //         return true; // Skip token verification for specified actions
    //     }

    //     $token = Yii::$app->request->getHeaders()->get('Authorization');
    //     if ($token !== null) {
    //         // Assuming you're using the token itself as the cache key
    //         $cacheKey = 'jwt_' . hash('sha256', $token);
    //         $tokenIsValid = Yii::$app->cache->get($cacheKey);

    //         if ($tokenIsValid === false) { // Token not in cache or cache expired
    //             if (!User::verifyJwt($token)) {
    //                 throw new \yii\web\UnauthorizedHttpException('Your token is invalid or expired.');
    //                 return false;
    //             } else {
    //                 // Token is valid, store it in cache. Adjust TTL as needed.
    //                 Yii::$app->cache->set($cacheKey, true, 3600); // 1 hour TTL
    //             }
    //         }
    //     } else {
    //         throw new \yii\web\UnauthorizedHttpException('No token provided.');
    //         return false;
    //     }

    //     return true; // Proceed with the action since the token is valid
    // }