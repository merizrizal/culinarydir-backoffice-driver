<?php

use kartik\grid\GridView;
use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel core\models\search\RegistryDriverSearch */
/* @var $title string */
/* @var $actionColumn array */
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
$this->params['breadcrumbs'][] = \Yii::t('app', 'Data Driver');

echo $ajaxRequest->component(false); ?>

<div class="registry-driver-index">

  	<?php
    $modalDialog = new ModalDialog([
        'clickedComponent' => 'a#delete',
        'modelAttributeId' => 'model-id',
        'modelAttributeName' => 'model-name',
    ]);

    $column = [
        ['class' => 'yii\grid\SerialColumn'],

        'created_at:date',
        'first_name',
        'phone',
        'number_plate',
    ];

    if (!empty($actionColumn)) {

        array_push($column, $actionColumn);
    }

    echo GridView::widget([
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
                'content' => Html::a('<i class="fa fa-sync-alt"></i>', ['index-' . strtolower($statusApproval)], [
                    'id' => 'refresh',
                    'class' => 'btn btn-success',
                    'data-placement' => 'top',
                    'data-toggle' => 'tooltip',
                    'title' => 'Refresh'
                ])
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => $column,
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
echo $modalDialog->renderDialog();

$jscript = ''
    . $modalDialog->getScript() . '

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