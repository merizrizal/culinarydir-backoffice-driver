<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;

/* @var $this yii\web\View */
/* @var $searchModel core\models\search\UserAsDriverSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'UserAsDriver',
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

$this->title = \Yii::t('app', 'Driver');
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component(true); ?>

<div class="user-as-driver-index">

    <?php
    $modalDialog = new ModalDialog([
        'clickedComponent' => 'a#delete',
        'modelAttributeId' => 'model-id',
        'modelAttributeName' => 'model-name',
    ]);

    echo GridView::widget([
        'id' => 'grid-view-user-as-driver',
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

            'user.username',
            'user.email',
            'total_cash:currency',
            [
                'attribute' => 'is_online',
                'format' => 'raw',
                'filter' => [true => 'True', false => 'False'],
                'value' => function ($model, $index, $widget) {

                    return Html::checkbox('is_online[]', $model->is_online, ['value' => $index, 'disabled' => 'disabled']);
                }
            ],
            [
                'attribute' => 'user.not_active',
                'format' => 'raw',
                'filter' => [true => 'True', false => 'False'],
                'value' => function ($model, $index, $widget) {

                    return Html::checkbox('not_active[]', $model->user->not_active, ['value' => $index, 'disabled' => 'disabled']);
                }
            ],

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
                            'model-id' => $model->user_id,
                            'model-name' => $model->user->full_name,
                        ]);
                    },
                ]
            ],
        ],
        'tableOptions' => [
            'class' => 'table table-striped table-hover'
        ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['id' => $model['user_id'], 'class' => 'row-grid-view-user-as-driver', 'style' => 'cursor: pointer;'];
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

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = ''
    . Yii::$app->params['checkbox-radio-script']()
    . '$(".iCheck-helper").parent().removeClass("disabled");'
    . $modalDialog->getScript() . '

    $("div.container.body").off("click");
    $("div.container.body").on("click", function(event) {

        if ($(event.target).parent(".row-grid-view-user-as-driver").length > 0) {

            $("td").not(event.target).popover("destroy");
        } else {
            $(".popover.in").popover("destroy");
        }
    });

    $(".row-grid-view-user-as-driver").popover({
        trigger: "click",
        placement: "top",
        container: ".row-grid-view-user-as-driver",
        html: true,
        selector: "td",
        content: function () {
            var content = $(this).parent().find(".btn-container").html();

            return $(content);
        }
    });

    $(".row-grid-view-user-as-driver").on("shown.bs.popover", function(event) {

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