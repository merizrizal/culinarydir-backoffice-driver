<?php

use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;
use yii\helpers\Html;

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

$this->title = $model['person']['first_name'] . " ". $model['person']['last_name'];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Person As Driver'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<?= $ajaxRequest->component() ?>

<div class="person-as-driver-view">

    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">

                <div class="x_content">

                    <?= Html::a('<i class="fa fa-pencil-alt"></i> Edit', ['update', 'id' => $model['person_id']], ['class' => 'btn btn-primary']) ?>

                    <?= Html::a('<i class="fa fa-trash-alt"></i> Delete', ['delete', 'id' => $model['person_id']], [
                            'id' => 'delete',
                            'class' => 'btn btn-danger',
                            'data-not-ajax' => 1,
                            'model-id' => $model['person_id'],
                        ]) ?>

                    <?= Html::a('<i class="fa fa-times"></i> Cancel', ['index'], ['class' => 'btn btn-default']) ?>

                    <div class="clearfix" style="margin-top: 15px"></div>

					<div class="row">
                        <div class="col-xs-12">
                            <h4><strong><?= Yii::t('app', 'Driver Name') ?></strong> : <?= $model['person']['first_name'] . " ". $model['person']['last_name']; ?></h4>
                        </div>
                    </div>

					<hr>

                    <div class="row">
                        <div class="col-xs-12">
                            <h4><strong><?= Yii::t('app', 'Driver Information') ?></strong></h4>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-20">
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'First Name')) ?><br>
                            <?= $model['person']['first_name'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Last Name')) ?><br>
                            <?= $model['person']['last_name'] ?>
                        </div>
                   		<div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Phone')) ?><br>
                            <?= $model['person']['phone'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Email')) ?><br>
                            <?= $model['person']['email'] ?>
                        </div>
                    </div>

                    <div class="row mb-20">
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Nomor KTP')) ?><br>
                            <?= $model['no_ktp'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Nomor SIM')) ?><br>
                            <?= $model['no_sim'] ?>
                        </div>
                   		<div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Date Birth')) ?><br>
                            <?= $model['date_birth'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'District')) ?><br>
                            <?= $model['district']['name'] ?>
                        </div>
                    </div>

                    <div class="row mb-20">
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Motor Brand')) ?><br>
                            <?= $model['motor_brand'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Motor Type')) ?><br>
                            <?= $model['motor_type'] ?>
                        </div>
                   		<div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Number Plate')) ?><br>
                            <?= $model['number_plate'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Stnk Expired')) ?><br>
                            <?= $model['stnk_expired'] ?>
                        </div>
                    </div>

                    <div class="row mb-20">
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Emergency Contact Name')) ?><br>
                            <?= $model['emergency_contact_name'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Emergency Contact Phone')) ?><br>
                            <?= $model['emergency_contact_phone'] ?>
                        </div>
                   		<div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Emergency Contact Address')) ?><br>
                            <?= $model['emergency_contact_address'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Other Driver')) ?><br>
                            <?= $model['other_driver'] ?>
                        </div>
                    </div>

                    <hr>

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