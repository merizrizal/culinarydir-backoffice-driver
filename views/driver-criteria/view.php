<?php

use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model core\models\DriverCriteria */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'DriverCriteria',
]);

$ajaxRequest->view();

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


$this->title = $model['personAsDriver']['person']['first_name'];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Driver Criteria'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<?= $ajaxRequest->component() ?>

<div class="driver-criteria-view">

    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="x_content">

                    <?= Html::a('<i class="fa fa-upload"></i> Create', ['create'], ['class' => 'btn btn-success']) ?>

                    <?= Html::a('<i class="fa fa-pencil-alt"></i> Edit', ['update', 'id' => $model['id']], ['class' => 'btn btn-primary']) ?>

                    <?= Html::a('<i class="fa fa-trash-alt"></i> Delete', ['delete', 'id' => $model['id']], [
                            'id' => 'delete',
                            'class' => 'btn btn-danger',
                            'data-not-ajax' => 1,
                            'model-id' => $model['id'],
                            'model-name' => $model['criteria_name'],
                        ]) ?>

                    <?= Html::a('<i class="fa fa-times"></i> Cancel', ['index'], ['class' => 'btn btn-default']) ?>

                    <div class="clearfix" style="margin-top: 15px"></div>

                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => [
                            'class' => 'table'
                        ],
                        'attributes' => [
                                        'id',
                        'person_as_driver_id',
                        'criteria_name',
                        'type',
                        'created_at',
                        'user_created',
                        'updated_at',
                        'user_updated',
                        ],
                    ]) ?>

                </div>
            </div>
        </div>
    </div>

</div>

<?php
$modalDialog = new ModalDialog([
    'clickedComponent' => 'a#delete',
    'modelAttributeId' => 'model-id',
    'modelAttributeName' => 'model-name',
]);

$modalDialog->theScript(false);

echo $modalDialog->renderDialog(); ?>