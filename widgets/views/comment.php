<?php
use yii\helpers\Html;

?>

<div class="media-left">
    <?php if ($userurl !== null): ?>
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
            <span class="rating-stars">
            <?php
            if ($rating == 0) {
                //echo \Yii::t('comments', 'Not rated');
            } else {
                for ($i = 0; $i < 5; $i++) {
                    if ($i < $rating) {
                        echo Html::tag('span', '', ['class' => 'glyphicon glyphicon-star']);
                    } else {
                        echo Html::tag('span', '', ['class' => 'glyphicon glyphicon-star-empty']);
                    }
                }
            }
            ?>
            </span>

            <div class="action pull-right">
                <?php if ($owner) { ?>
                    <a class="delete" href="#">
                        <span class="glyphicon glyphicon-remove"></span><?= \Yii::t('frontend', 'Delete'); ?>
                    </a>
                    <a class="edit" href="#">
                        <span class="glyphicon glyphicon-pencil"></span><?= \Yii::t('frontend', 'Edit'); ?>
                    </a>
                <?php } ?>
                <a class="reply">
                    <span class="glyphicon glyphicon-retweet"></span> <?= \Yii::t('frontend', 'Reply'); ?>
                </a>
            </div>
        </div>
        <div class="comment-body"><?= $body; ?></div>

    </div>
</div>
