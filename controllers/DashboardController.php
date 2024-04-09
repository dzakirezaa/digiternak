<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\auth\HttpBearerAuth;
use app\models\Cage;
use app\models\Livestock;

class DashboardController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Menambahkan authenticator untuk otentikasi menggunakan access token
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options'], // Tambahkan action yang tidak memerlukan otentikasi di sini
        ];

        return $behaviors;
    }

    /**
     * Menampilkan data dashboard berdasarkan ID user.
     * @param integer $userId
     * @return mixed
     */
    public function actionDashboard($userId)
    {
        // Get jumlah kandang pada user tersebut
        $totalCages = Cage::find()->where(['person_id' => $userId])->count();

        // Get jumlah ternak pada user tersebut
        $totalLivestocks = Livestock::find()->where(['person_id' => $userId])->count();

        return [
            'total_cages' => $totalCages,
            'total_livestocks' => $totalLivestocks,
        ];
    }
}
