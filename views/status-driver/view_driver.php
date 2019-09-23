<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryDriver */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'RegistryDriver',
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

$this->title = $model['first_name'] . ' ' . $model['last_name'];
$this->params['breadcrumbs'][] = $this->title; ?>

<?= $ajaxRequest->component() ?>

<div class="registry-driver-view">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h4><strong><?= Yii::t('app', 'User In Charge') ?></strong> : <?= $model['userInCharge']['full_name'] ?></h4>
                </div>
                
                <div class="x_content">
                    <div class="row mb-20">
                        <div class="col-md-3">
                                <?= Html::label(Yii::t('app', 'First Name')) ?><br>
                                <?= $model['first_name'] ?>
                            </div>
                            <div class="col-md-3">
                                <?= Html::label(Yii::t('app', 'Last Name')) ?><br>
                                <?= $model['last_name'] ?>
                            </div>
                            <div class="col-md-3">
                                <?= Html::label(Yii::t('app', 'Phone')) ?><br>
                                <?= $model['phone'] ?>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-md-3">
                                <?= Html::label(Yii::t('app', 'No KTP')) ?><br>
                                <?= $model['no_ktp'] ?>
                            </div>
                            <div class="col-md-9">
                                <?= Html::label(Yii::t('app', 'No SIM')) ?><br>
                                <?= $model['no_sim'] ?>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-lg-3 col-xs-6">
                                <?= Html::label(Yii::t('app', 'Motor Type')) ?><br>
                                <?= $model['motor_type'] ?>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <?= Html::label(Yii::t('app', 'Motor Brand')) ?><br>
                                <?= $model['motor_brand'] ?>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <?= Html::label(Yii::t('app', 'Number Plate')) ?><br>
                                <?= $model['number_plate'] ?>
                            </div>
                        </div>
                    </div>
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