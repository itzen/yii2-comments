<?php

namespace itzen\comments;

use Yii;

class Module extends \yii\base\Module
{


    /**
     * @inheritdoc
     */
    public $defaultRoute = 'comment';


    public $defaultAvatar;


    public $defaultStatusId = 3;

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'itzen\comments\controllers';

    /**
     * @var string
     * Translate category used in Yii::t() function
     */
    public static $translateCategory = 'common';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->i18n->translations[self::$translateCategory]) && !isset(Yii::$app->i18n->translations['*'])) {
            Yii::$app->i18n->translations[self::$translateCategory] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@itzen/comments/messages'
            ];
        }
        $view = Yii::$app->getView();
        $assets = CommentsAsset::register($view);

        if ($this->defaultAvatar === null) {
            $this->defaultAvatar = $assets->baseUrl . '/avatar.png';
        }
    }



}
