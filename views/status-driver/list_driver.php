<?php

use kartik\grid\GridView;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel core\models\search\RegistryDriverSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $title string */
/* @var $statusApproval string */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'RegistryDriver',
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

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Approval Driver'), 'url' => ['status-driver/pndg-driver']];
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component(false) ?>

<div class="registry-driver-index">

    <?= GridView::widget([
        'id' => 'grid-view-registry-driver',
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
                'content' => Html::a('<i class="fa fa-sync-alt"></i>', [strtolower($statusApproval) . '-driver'], [
                    'id' => 'refresh',
                    'class' => 'btn btn-success',
                    'data-placement' => 'top',
                    'data-toggle' => 'tooltip',
                    'title' => 'Refresh'
                ])
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'created_at:date',
            'first_name',
            'phone',
            'number_plate',
            'userInCharge.full_name',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '
                    <div class="btn-container hide">
                        <div class="visible-lg visible-md">
                            <div class="btn-group btn-group-md" role="group" style="width: 40px">
                                {view}
                            </div>
                        </div>
                        <div class="visible-sm visible-xs">
                            <div class="btn-group btn-group-lg" role="group" style="width: 52px">
                                {view}
                            </div>
                        </div>
                    </div>',
                'buttons' => [
                    'view' => function($url, $model, $key) {

                        return Html::a('<i class="fa fa-search-plus"></i>', ['view-driver', 'id' => $model->id, 'appDriverId' => $model->application_driver_id], [
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

            return ['id' => $model['id'], 'class' => 'row-grid-view-registry-driver', 'style' => 'cursor: pointer;'];
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

        if ($(event.target).parent(".row-grid-view-registry-driver").length > 0) {

            $("td").not(event.target).popover("destroy");
        } else {
            $(".popover.in").popover("destroy");
        }
    });

    $(".row-grid-view-registry-driver").popover({
        trigger: "click",
        placement: "top",
        container: ".row-grid-view-registry-driver",
        html: true,
        selector: "td",
        content: function () {
            var content = $(this).parent().find(".btn-container").html();

            return $(content);
        }
    });

    $(".row-grid-view-registry-driver").on("shown.bs.popover", function(event) {

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