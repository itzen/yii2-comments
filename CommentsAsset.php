<?php

namespace itzen\comments;

class CommentsAsset extends \yii\web\AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@itzen/comments/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'comments.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init() {
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }

    public static function publishImages() {
        \Yii::$app->assetManager->publish(__DIR__ . "/assets");
    }

}
