<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\core\Comment $model
 */

$this->title = Yii::t('common', 'Create {modelClass}', [
    'modelClass' => 'Comment',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Comments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comment-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
