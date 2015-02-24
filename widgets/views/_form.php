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
        $form = ActiveForm::begin(
            [
                'id' => 'comment-form',
                'type' => ActiveForm::TYPE_VERTICAL,
                // 'action' => '/comments/comment/create',
                'enableAjaxValidation' => true,
            ]);
        echo $form->errorSummary($model);

        echo $form->field($model, 'object_id')->hiddenInput()->label(false);
        echo $form->field($model, 'object_key')->hiddenInput()->label(false);
        echo $form->field($model, 'parent_id')->hiddenInput()->label(false);

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
        echo Html::submitButton($model->isNewRecord ? Yii::t('common', 'Add comment') : Yii::t('common', 'Update comment'), ['class' => $model->isNewRecord ? 'btn btn-success btn-add-comment' : 'btn btn-primary btn-add-comment']);
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

<?php
$js = <<<JS

jQuery('#comment-form').on('click', '.btn-add-comment', function () {
    var self = this;
    var url = '/comments/comment/create';
    jQuery('#comment-parent_id').val(jQuery('.comment-form').parent().data('element-id'));
    jQuery.ajax({
        'url': url,
        'type': 'POST',
        'data': jQuery('#comment-form').serialize(),
        'dataType': 'JSON',
        'success': function (data) {
            if (data.status === 'success') {
               var element = jQuery('.comments-tree');
               if (data.data.parentId) {
                    var replayElement = jQuery(element).find('[data-element-id="'+data.data.parentId+'"]');
               } else {
                    replayElement =  element.children();
               }
               (data.data.firstElement && !data.data.parentId) ? element.append(data.data.renderedLastComment) : replayElement.append(data.data.renderedLastComment);
               $('#commentsCount').text(data.data.commentsCount);
               $('#comment-form')[0].reset();
               $('.redactor-editor').html('');
               moveForm(element.parent(), true);
               replyClickHandler();
               deleteClickHandler();
            } else {

            }
          jQuery.growl({
                    title: '<strong class="growl-title">'+data.status+'</strong><hr/>',
                    message: data.message
                },
                {
                    type: data.type,
                    delay: 10000
                }
            );
        }
    });
});

function moveForm(targetElement, startPosition) {
    var form = jQuery('.comment-form');
    var newPlace = targetElement;
    form.slideUp('fast', function(){
        form.detach();
        if (newPlace.find('.media-list:first').length && !startPosition) {
            newPlace.find('.media-list:first').prepend(form);
        } else {
            newPlace.append(form);
        }

        form.slideDown('fast');
    });
}

function replyClickHandler() {
    jQuery('.reply').click(function () {
        moveForm(jQuery(this).parent().parent().parent().parent().parent(), false);
    });
}

$(document).ready(function(){
    replyClickHandler()
});


jQuery('.comments-tree').on('click', '.delete', function (event) {
    event.preventDefault();
    var url = '/comments/comment/remove-comment';
    jQuery.ajax({
        'url': url,
        'type': 'POST',
        'data': 'id=' + jQuery(this).parent().parent().parent().parent().parent().data('element-id'),
        'dataType': 'JSON',
        'success': function (data) {
            if (data.status === 'success') {
                var element = jQuery('.comments-tree');
                jQuery(element).find('[data-element-id="'+data.data+'"]').remove();
            } else {

            }
          jQuery.growl({
                    title: '<strong class="growl-title">'+data.status+'</strong><hr/>',
                    message: data.message
                },
                {
                    type: data.type,
                    delay: 10000
                }
            );
        }
    });
});

function deleteClickHandler() {
jQuery('.delete').click(function (event) {
    event.preventDefault();
    });
}

JS;

$this->registerJs($js);
?>
