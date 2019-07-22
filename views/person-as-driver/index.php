<?php

use kartik\grid\GridView;
use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
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
$this->params['breadcrumbs'][] = $this->title; ?>

<?= $ajaxRequest->component(true) ?>

<div class="person-as-driver-index">

    <?php
    $modalDialog = new ModalDialog([
        'clickedComponent' => 'a#delete',
        'modelAttributeId' => 'model-id',
        'modelAttributeName' => 'model-name',
    ]); ?>

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
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],


            'district_id',
            'no_ktp',
            'no_sim',
            'date_birth',
            'motor_brand',
            'motor_type',
            //'emergency_contact_name',
            //'emergency_contact_phone',
            //'emergency_contact_address:ntext',
            //'number_plate',
            //'stnk_expired',
            //'created_at',
            //'user_created',
            //'updated_at',
            //'user_updated',
            //'other_driver',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '
                    <div class="btn-container hide">
                        <div class="visible-lg visible-md">
                            <div class="btn-group btn-group-md" role="group" style="width: 120px">
                                {view}{update}{delete}
                            </div>
                        </div>
                        <div class="visible-sm visible-xs">
                            <div class="btn-group btn-group-lg" role="group" style="width: 156px">
                                {view}{update}{delete}
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
                    'update' => function($url, $model, $key) {
                        return Html::a('<i class="fa fa-pencil-alt"></i>', $url, [
                            'id' => 'update',
                            'class' => 'btn btn-success',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Edit',
                        ]);
                    },
                    'delete' => function($url, $model, $key) {
                        return Html::a('<i class="fa fa-trash-alt"></i>', $url, [
                            'id' => 'delete',
                            'class' => 'btn btn-danger',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'data-not-ajax' => 1,
                            'title' => 'Delete',
                            'model-id' => $model->person_id,
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

<?= $modalDialog->renderDialog() ?>

<?php
$jscript = ''
    . $modalDialog->getScript() . '

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