<?php
use yii\helpers\Html;

?>

<div class="media-left">
    <?php if($userurl!==null):?>
    <a href="<?= $userurl; ?>">
        <div class="thumbnail">
            <?= $avatar; ?>
        </div>
    </a>
    <?php else: ?>
        <div class="thumbnail">
            <?= $avatar; ?>
        </div>
    <?php endif; ?>
</div>
<div class="media-body">
    <div class="well">
        <div class="comment-meta">
            <?= \Yii::t('comments', 'By') . ': ' . $username; ?> <?= \Yii::t('comments', 'on {0, date}', $date); ?>

            <div class="action pull-right">
                <a class="reply" href="#">
                    <span class="glyphicon glyphicon-retweet"></span> <?= \Yii::t('frontend', 'Reply'); ?>
                </a>
            </div>
        </div>
        <div class="comment-body"><?= Html::encode($body); ?></div>

    </div>
</div>
