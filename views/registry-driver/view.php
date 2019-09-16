<?php

use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;
use sycomponent\Tools;
use yii\helpers\Html;

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

$this->title = $model->first_name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Registry Driver'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<?= $ajaxRequest->component() ?>

<div class="registry-driver-view">

    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="x_content">

                   	<?php
                    if (!empty($actionButton)) {

                        foreach ($actionButton as $valActionButton) {

                            echo $valActionButton($model);
                        }
                    } ?>

                   	<?= Html::a('<i class="fa fa-times"></i> Cancel', ['index'], ['class' => 'btn btn-default']) ?>

                    <div class="clearfix" style="margin-top: 15px"></div>

					<div class="row">
                        <div class="col-xs-12">
                            <h4><strong><?= \Yii::t('app', 'Driver Name') ?></strong> : <?= $model['first_name'] . " " . $model['last_name']; ?></h4>
                        </div>
                    </div>

					<hr>

                    <div class="row">
                        <div class="col-xs-12">
                            <h4><strong><?= \Yii::t('app', 'Driver Information') ?></strong></h4>
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

					<div class="row">
                        <div class="col-xs-12">
                            <h4><strong><?= \Yii::t('app', 'Driver Attachment') ?></strong></h4>
                        </div>
                    </div>

                    <hr>

                    <div class="row">

                        <?php
                        if (!empty($model['registryDriverAttachments'])):

                            foreach ($model['registryDriverAttachments'] as $dataDriverAttachments): ?>

                                <div class="col-xs-6 col-sm-3">
                                    <div class="thumbnail">
                                        <div class="image view view-first">

                                            <?= Html::img(\Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/driver_attachment/', $dataDriverAttachments['file_name'], 200, 150), ['style' => 'width: 100%; display: block;']);  ?>

                                            <div class="mask">
                                                <p>&nbsp;</p>
                                                <div class="tools tools-bottom">
                                                    <a class="show-image direct" href="<?= \Yii::getAlias('@uploadsUrl') . '/img/driver_attachment/' . $dataDriverAttachments['file_name'] ?>"><i class="fa fa-search"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php
                            endforeach;
                        endif; ?>

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
]);

$modalDialog->theScript(false);

echo $modalDialog->renderDialog(); ?>