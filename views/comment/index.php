<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\detail\DetailViewAsset;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\core\models\search\Comment */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Comments');
$this->params['breadcrumbs'][] = $this->title;

DetailViewAsset::register($this);
$this->registerJs('jQuery("#pjax-grid").on("kvexprow.loaded","#grid",function(event){jQuery(".kv-expanded-row div form").kvDetailView();});');
$this->registerJs('jQuery("#pjax-grid").on("submit", ".kv-expanded-row div form", function(e){ 
    var self = this;
    e.preventDefault();
    jQuery.ajax({
        type: "POST",
        url: jQuery(self).attr("action"),
        data: jQuery(self).serialize(),
        success: function(data){
            if(data === "success"){
                jQuery.pjax.reload({container: "#pjax-grid"});
            }
            else{
                jQuery(self).parents(".kv-expanded-row").html(data);
            }
        }
    });
    return false;
})');
?>
<div class="comment-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'grid',
        'dataProvider' => $dataProvider,
        'toolbar' => [
            ['content' =>
                Html::a('<i class="glyphicon glyphicon-plus"></i> Create', ['create'], ['class' => 'btn btn-success', 'data-pjax' => '0']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['class' => 'btn btn-default', 'title' => Yii::t('backend', 'Reset Grid')])
            ],
            '{export}',
            '{toggleData}'
        ],
        'panel' => [
            'heading' => "<h3 class=\"panel-title\">$this->title</h3>",
            'type' => 'default',
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
		'id' => 'pjax-grid'
            ]
        ],
        'exportConfig' => [
            GridView::CSV => [],
            GridView::HTML => [],
            GridView::PDF => [
                'config' => [
                    'mode' => 'utf-8',
                ]
            ],
            GridView::JSON => [],
            GridView::TEXT => [],
            GridView::EXCEL => [],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'sortorder',
            'status_id',
            'user_id',
            'created_at',
            // 'updated_at',
            // 'created_by',
            // 'updated_by',
            // 'username',
            // 'email:email',
            // 'website',
            // 'body:ntext',
            // 'rating',
            // 'object_id',
            // 'object_key',
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'value' => 'expandalbe',
                'detailUrl' => 'partial-view',
                'expandIcon' => '<span class="glyphicon glyphicon-chevron-right text-primary"></span>',
                'collapseIcon' => '<span class="glyphicon glyphicon-chevron-down text-primary"></span>',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
