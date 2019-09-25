<?php

use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model core\models\RegistryDriver */
/* @var $id string */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'RegistryDriver',
]);

$ajaxRequest->form();

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) {

    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();
}

$this->title = 'Check & Set ' . Yii::t('app', 'Driver Information') . ' : ' . $model['first_name'];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Approval Driver'), 'url' =>  ['status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId]];
$this->params['breadcrumbs'][] = $model['first_name'] . ' ' . $model['last_name']; ?>

<?= $ajaxRequest->component(); ?>

<div class="registry-driver-form">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="x_content">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'registry-driver-form',
                        'options' => [

                        ]
                    ]);

                        echo Html::hiddenInput('check_set_driver_info', true);

                        echo Html::submitButton('<i class="fa fa-check-circle"></i> OK & Save', ['class' => 'btn btn-success']);
                        echo ' ' . Html::a('<i class="fa fa-pencil-alt"></i> Edit', ['registry-driver-approval/update-driver-info', 'id' => $id, 'appDriverId' => $appDriverId, 'actid' => $actid, 'logsaid' => $logsaid], ['class' => 'btn btn-primary']);
                        echo ' ' . Html::a('<i class="fa fa-times"></i> Cancel', ['status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId], ['class' => 'btn btn-default']); ?>

                        <div class="clearfix" style="margin-top: 15px"></div>

						<div class="row">
                            <div class="col-xs-12">
                                <h4><strong><?= Yii::t('app', 'Status') ?></strong> : <?= $model['applicationDriver']['logStatusApprovalDrivers'][0]['statusApprovalDriver']['name'] ?></h4>
                            </div>
                            <div class="col-xs-12">
                                <h4><strong><?= Yii::t('app', 'User In Charge') ?></strong> : <?= $model['userInCharge']['full_name'] ?></h4>
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
                            <?= Html::label(\Yii::t('app', 'First Name')) ?><br>
                            <?= $model['first_name'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'Last Name')) ?><br>
                            <?= $model['last_name'] ?>
                        </div>
                   		<div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'Phone')) ?><br>
                            <?= $model['phone'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'Email')) ?><br>
                            <?= $model['email'] ?>
                        </div>
                    </div>

                    <div class="row mb-20">
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'No Ktp')) ?><br>
                            <?= $model['no_ktp'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'No Sim')) ?><br>
                            <?= $model['no_sim'] ?>
                        </div>
                   		<div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'Date Birth')) ?><br>
                            <?= $model['date_birth'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'District')) ?><br>
                            <?= $model['district']['name'] ?>
                        </div>
                    </div>

                    <div class="row mb-20">
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'Motor Brand')) ?><br>
                            <?= $model['motor_brand'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'Motor Type')) ?><br>
                            <?= $model['motor_type'] ?>
                        </div>
                   		<div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'Number Plate')) ?><br>
                            <?= $model['number_plate'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'Stnk Expired')) ?><br>
                            <?= $model['stnk_expired'] ?>
                        </div>
                    </div>

                    <div class="row mb-20">
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'Emergency Contact Name')) ?><br>
                            <?= $model['emergency_contact_name'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'Emergency Contact Phone')) ?><br>
                            <?= $model['emergency_contact_phone'] ?>
                        </div>
                   		<div class="col-xs-6 col-sm-3">
                            <?= Html::label(\Yii::t('app', 'Emergency Contact Address')) ?><br>
                            <?= $model['emergency_contact_address'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">

                            <?= Html::label(\Yii::t('app', 'Other Driver ?')) ?><br>
                            <?= !empty($model['other_driver']) ? $model['other_driver'] : 'Tidak Ada'; ?>

                        </div>
                    </div>

                    <div class="row mb-20">
                    	<div class="col-xs-8 col-sm-3">

                            <?= Html::label(\Yii::t('app', 'Is Criteria Passed')) ?><br>
                            <?= $model['is_criteria_passed'] ? 'Lulus Pengecekan' : 'Belum lulus Pengecekan'; ?>

                        </div>
                    </div>

                    <hr>

                        <?php
                        echo Html::submitButton('<i class="fa fa-check-circle"></i> OK & Save', ['class' => 'btn btn-success']);
                        echo ' ' . Html::a('<i class="fa fa-pencil-alt"></i> Edit', ['registry-driver/update-driver-info', 'id' => $id, 'appDriverId' => $appDriverId, 'actid' => $actid, 'logsaid' => $logsaid], ['class' => 'btn btn-primary']);
                        echo ' ' . Html::a('<i class="fa fa-times"></i> Cancel', ['status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId], ['class' => 'btn btn-default']);

                    ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
</div>