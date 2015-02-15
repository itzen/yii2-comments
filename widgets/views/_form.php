<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var itzen\comments\models\Comment $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="comment-form">
    <?php if (Yii::$app->user->can('/comments/comment/create')): ?>
        <?php
        $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL, 'action' => '/comments/comment/create']);
        echo $form->errorSummary($model);

        echo $form->field($model, 'object_id')->hiddenInput()->label(false);
        echo $form->field($model, 'object_key')->hiddenInput()->label(false);

        echo $form->field($model, 'rating')->widget(\kartik\rating\StarRating::classname(), [
            'pluginOptions' => [
                'step' => 1,
                'size' => 'xs',
                'starCaptions' => Yii::$app->params['starCaptions'],
            ]
        ]);

        if (Yii::$app->user->isGuest) {
            echo $form->field($model, 'username');
            echo $form->field($model, 'email');
            echo $form->field($model, 'website');
        }

        echo $form->field($model, 'body')->widget(\yii\imperavi\Widget::className(), [
            'id' => Html::getInputId($model, 'body'), // !!!
            'model' => $model,
            'attribute' => 'body',
            'options' => [
                'minHeight' => 100,
                'toolbarFixed' => false,
                'buttonSource' => false,
            ]

        ]);

        \yii\helpers\Url::remember('', 'comment');
        echo Html::submitButton($model->isNewRecord ? Yii::t('common', 'Add comment') : Yii::t('common', 'Update comment'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
        ActiveForm::end();
        ?>
    <?php else: ?>
        <div class="alert alert-info">
            <?= Yii::t('common', 'Only logged in user can comment.'); ?>
            <hr/>
            <?= Html::a(Yii::t('common', 'Login'), Yii::$app->user->loginUrl, ['class' => 'btn btn-primary login-modal']); ?>

        </div>


    <?php endif; ?>

</div>
