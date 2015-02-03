<div class="panel panel-default widget">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="glyphicon glyphicon-comment"></span>
            <?= \Yii::t('frontend', 'Comments'); ?>
            <span class="label label-info pull-right">
                <?= $commentsCount; ?>
            </span>
        </h3>

    </div>
    <div class="panel-body">
        <div class="media comments-tree">
            <?= $comments; ?>
        </div>
        <?= $this->render('_form', [
            'model' => $model
        ]); ?>
    </div>
</div>