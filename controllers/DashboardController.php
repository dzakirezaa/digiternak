<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\auth\HttpBearerAuth;
use app\models\Cage;
use app\models\Livestock;
use app\models\User;

class DashboardController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Menambahkan authenticator untuk otentikasi menggunakan access token
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options'],
        ];

        return $behaviors;
    }

    /**
     * Menampilkan data dashboard berdasarkan ID user.
     * @param integer $userId
     * @return mixed
     */
    public function actionUserOverview($userId)
    {
        $user = User::findOne($userId);

        if ($user === null) {
            return [
                'message' => 'Pengguna tidak ditemukan.',
                'error' => true,
            ];
        }

        try {
            // Get jumlah kandang pada user tersebut
            $totalCages = Cage::find()->where(['user_id' => $userId])->count();

            // Get jumlah ternak pada user tersebut
            $totalLivestocks = Livestock::find()->where(['user_id' => $userId])->count();

            // If the queries were successful, return the counts, a success message, and an error status of false
            return [
                'message' => 'Berhasil mendapatkan ringkasan data pengguna.',
                'error' => false,
                'data' => [
                    'total_cages' => $totalCages,
                    'total_livestocks' => $totalLivestocks,
                ],
            ];
        } catch (\Exception $e) {
            // If the queries failed, return an error message, an error status of true, and the exception message
            return [
                'message' => 'Gagal mendapatkan ringkasan data pengguna.',
                'error' => true,
                'details' => $e->getMessage(),
            ];
        }
    }
}
