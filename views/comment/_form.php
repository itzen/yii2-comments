<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var common\models\core\Comment $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="comment-form">

    <?php
    $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL]);
    echo $form->errorSummary($model);
    echo Form::widget([
        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [
            'sortorder' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('common', 'Enter Sortorder...')]],
            'status_id' => ['type' => Form::INPUT_DROPDOWN_LIST, 'items' => $model->availableStatuses, 'options' => ['placeholder' => Yii::t('common', 'Choose status...')]],
            'user_id' => ['type' => Form::INPUT_DROPDOWN_LIST, 'items' => $model->availableUsers, 'options' => ['placeholder' => Yii::t('common', 'Choose user...')]],
            'rating' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('common', 'Enter Rating...')]],
            'object_id' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('common', 'Enter Object Id...')]],
            'body' => ['type' => Form::INPUT_TEXTAREA, 'options' => ['placeholder' => Yii::t('common', 'Enter Body...'), 'rows' => 6]],
            'object_key' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('common', 'Enter Object Key...')]],
            'username' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('common', 'Enter Username...'), 'maxlength' => 45]],
            'email' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('common', 'Enter Email...'), 'maxlength' => 45]],
            'website' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => Yii::t('common', 'Enter Website...'), 'maxlength' => 128]],
        ]
    ]);
    echo Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end();
    ?>

</div>
