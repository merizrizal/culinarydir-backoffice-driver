<?php

use kartik\grid\GridView;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel core\models\search\PersonAsDriverSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelPerson core\models\Person */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'PersonAsDriver',
]);

$ajaxRequest->index();

$status = \Yii::$app->session->getFlash('status');
$message1 = \Yii::$app->session->getFlash('message1');
$message2 = \Yii::$app->session->getFlash('message2');

if ($status !== null) {
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();
}

$this->title = \Yii::t('app', 'Person As Driver');
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component(false); ?>

<div class="person-as-driver-index">

    <?= GridView::widget([
        'id' => 'grid-view-person-as-driver',
        'dataProvider' => $dataProvider,
        'pjax' => false,
        'bordered' => false,
        'panelHeadingTemplate' => '
            <div class="kv-panel-pager pull-right" style="text-align:right">
                {pager}{summary}
            </div>
            <div class="clearfix"></div>'
        ,
        'panelFooterTemplate' => '
            <div class="kv-panel-pager pull-right" style="text-align:right">
                {summary}{pager}
            </div>
            {footer}
            <div class="clearfix"></div>'
        ,
        'panel' => [
            'heading' => '',
        ],
        'toolbar' => [
            [
                'content' => Html::a('<i class="fa fa-sync-alt"></i>', ['index'], [
                    'id' => 'refresh',
                    'class' => 'btn btn-success',
                    'data-placement' => 'top',
                    'data-toggle' => 'tooltip',
                    'title' => 'Refresh'
                ])
            ],
            '{export}',
            '{toggleData}',
        ],
        'exportConfig' => [
            GridView::EXCEL => ['label' => 'Save as EXCEL'],
        ],
        'export' => [
            'showConfirmAlert' => false,
            'header' => ''
        ],
        'toggleDataOptions' => [
            'all' => [
                'label' => 'Tampilkan Semua',
            ],
            'page' => [
                'label' => 'Tampilkan Per Halaman',
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'person.first_name',
            'no_ktp',
            'no_sim',
            'person.phone',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '
                    <div class="btn-container hide">
                        <div class="visible-lg visible-md">
                            <div class="btn-group btn-group-md" role="group" style="width: 43px">
                                {view}
                            </div>
                        </div>
                        <div class="visible-sm visible-xs">
                            <div class="btn-group btn-group-lg" role="group" style="width: 50px">
                                {view}
                            </div>
                        </div>
                    </div>',
                'buttons' => [
                    'view' => function($url, $model, $key) {

                        return Html::a('<i class="fa fa-search-plus"></i>', $url, [
                            'id' => 'view',
                            'class' => 'btn btn-primary',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'View',
                        ]);
                    },
                ]
            ],
        ],
        'tableOptions' => [
            'class' => 'table table-striped table-hover'
        ],
        'rowOptions' => function ($model, $key, $index, $grid) {

            return ['id' => $model['person_id'], 'class' => 'row-grid-view-person-as-driver', 'style' => 'cursor: pointer;'];
        },
        'pager' => [
            'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
            'prevPageLabel' => '<i class="fa fa-angle-left"></i>',
            'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
            'nextPageLabel' => '<i class="fa fa-angle-right"></i>',
        ],
    ]); ?>

</div>

<?php
$jscript = '
    $("div.container.body").off("click");

    $("div.container.body").on("click", function(event) {

        if ($(event.target).parent(".row-grid-view-person-as-driver").length > 0) {

            $("td").not(event.target).popover("destroy");
        } else {
            $(".popover.in").popover("destroy");
        }
    });

    $(".row-grid-view-person-as-driver").popover({
        trigger: "click",
        placement: "top",
        container: ".row-grid-view-person-as-driver",
        html: true,
        selector: "td",
        content: function () {
            var content = $(this).parent().find(".btn-container").html();

            return $(content);
        }
    });

    $(".row-grid-view-person-as-driver").on("shown.bs.popover", function(event) {

        $(\'[data-toggle="tooltip"]\').tooltip();

        var popoverId = $(event.target).attr("aria-describedby");

        $(document).on("click", "#" + popoverId + " a", function(event) {

            if ($(this).attr("data-not-ajax") == undefined) {
                ajaxRequest($(this));
            }

            return false;
        });
    });
';

$this->registerJs($jscript); ?>