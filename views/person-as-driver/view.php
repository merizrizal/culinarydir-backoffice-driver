<?php

use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model core\models\PersonAsDriver */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'PersonAsDriver',
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

$this->title = $model->person_id;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Person As Driver'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<?= $ajaxRequest->component() ?>

<div class="person-as-driver-view">

    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">

                <div class="x_content">

                    <?= Html::a('<i class="fa fa-upload"></i> Create', ['create'], ['class' => 'btn btn-success']) ?>

                    <?= Html::a('<i class="fa fa-pencil-alt"></i> Edit', ['update', 'id' => $model->person_id], ['class' => 'btn btn-primary']) ?>

                    <?= Html::a('<i class="fa fa-trash-alt"></i> Delete', ['delete', 'id' => $model->person_id], [
                            'id' => 'delete',
                            'class' => 'btn btn-danger',
                            'data-not-ajax' => 1,
                            'model-id' => $model->person_id,
                        ]) ?>

                    <?= Html::a('<i class="fa fa-times"></i> Cancel', ['index'], ['class' => 'btn btn-default']) ?>

                    <div class="clearfix" style="margin-top: 15px"></div>

                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => [
                            'class' => 'table'
                        ],
                        'attributes' => [
                                        'person_id',
                        'district_id',
                        'no_ktp',
                        'no_sim',
                        'date_birth',
                        'motor_brand',
                        'motor_type',
                        'emergency_contact_name',
                        'emergency_contact_phone',
                        'emergency_contact_address:ntext',
                        'number_plate',
                        'stnk_expired',
                        'created_at',
                        'user_created',
                        'updated_at',
                        'user_updated',
                        'other_driver',
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