<?php

namespace itzen\comments;

use Yii;

class Module extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'comment';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'itzen\comments\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['itzen'])) {
            Yii::$app->i18n->translations['itzen'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@itzen/comments/messages'
            ];
        }
    }

}
