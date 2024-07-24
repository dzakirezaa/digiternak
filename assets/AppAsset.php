<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'assets/scss/iconly.scss',
        'assets/extensions/quill/quill.snow.css',
        'assets/extensions/quill/quill.bubble.css',
        'assets/extensions/apexcharts/apexcharts.min.css',
    ];
    public $js = [
        'assets/extensions/quill/quill.min.js',
        'assets/extensions/apexcharts/apexcharts.min.js',
        'assets/static/js/pages/dashboard.js',
        'assets/static/js/pages/quill.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}
