<?php

use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryDriver */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'RegistryDriver',
]);

$ajaxRequest->form();

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

<?= $ajaxRequest->component(false) ?>

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

    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h4><?= Yii::t('app', 'Driver Status') ?></h4>
                </div>

                <div class="x_content">

					<?php
                    $form = ActiveForm::begin([
                        'id' => 'registry-driver-form',
                        'action' => ['update-status-driver', 'id' => $model['applicationDriver']['id'], 'rdid' => $model['id']],
                    ]);

                        foreach ($model['applicationDriver']['logStatusApprovalDrivers'] as $dataLogStatusApprovalDriver):

                            if ($dataLogStatusApprovalDriver['is_actual']): ?>

                            	<div class="row">
                                    <div class="col-md-3">
                                        <h4><strong><?= $dataLogStatusApprovalDriver['status_approval_driver_id'] ?> <small><?= $dataLogStatusApprovalDriver['statusApprovalDriver']['name'] ?></small></strong></h4>
                                    </div>

                                    <div class="col-md-5">

										<?= $dataLogStatusApprovalDriver['statusApprovalDriver']['note'] ?>

                                        <div class="clearfix" style="margin-bottom: 5px"></div>

                                        <?php
                                        foreach ($dataLogStatusApprovalDriver['statusApprovalDriver']['statusApprovalDriverActions'] as $dataStatusApprovalDriverAction) {

                                            $btn = ' btn-default';
                                            $url = [$dataStatusApprovalDriverAction['url'], 'id' => $model['id'], 'appDriverId' => $model['applicationDriver']['id'], 'actid' => $dataStatusApprovalDriverAction['id'], 'logsaid' => $dataLogStatusApprovalDriver['id']];

                                            if (!empty($dataStatusApprovalDriverAction['logStatusApprovalDriverActions'])) {

                                                foreach ($dataStatusApprovalDriverAction['logStatusApprovalDriverActions'] as $value) {

                                                    if ($value['logStatusApprovalDriver']['application_driver_counter'] == $model['applicationDriver']['counter']) {

                                                        $btn = ' btn-success btn-action';
                                                        $url = '';
                                                        break;
                                                    }
                                                }
                                            }

                                            echo Html::a('<i class="fa fa-external-link-alt"></i> '. $dataStatusApprovalDriverAction['name'], $url, ['class' => 'btn btn-xs' . $btn, 'data-method' => 'post']) . '&nbsp';
                                        } ?>

									</div>

									<div class="col-md-4">

                                        <?php
                                        foreach ($dataLogStatusApprovalDriver['statusApprovalDriver']['statusApprovalDriverRequires0'] as $dataStatusApprovalDriverRequire) {

                                            $flag = true;

                                            if (!empty($dataStatusApprovalDriverRequire['statusApprovalDriver']['logStatusApprovalDrivers'])) {

                                                foreach ($dataStatusApprovalDriverRequire['statusApprovalDriver']['logStatusApprovalDrivers'] as $value) {

                                                    if ($value['application_driver_counter'] == $model['applicationDriver']['counter']) {

                                                        $flag = false;
                                                        break;
                                                    }
                                                }
                                            }

                                            if ($flag) {

                                                $btn = $dataStatusApprovalDriverRequire['statusApprovalDriver']['condition'] ? ' btn-primary' : ' btn-danger';

                                                echo Html::button('<i class="fa fa-arrow-circle-right"></i> ' . $dataStatusApprovalDriverRequire['statusApprovalDriver']['id'], [
                                                    'class' => 'btn' . $btn . ' submit-approval-driver',
                                                    'data-toggle' => 'tooltip',
                                                    'data-placement' => 'top',
                                                    'data-status-approval-driver-id' => $dataStatusApprovalDriverRequire['statusApprovalDriver']['id'],
                                                    'title' => $dataStatusApprovalDriverRequire['statusApprovalDriver']['name']
                                                ]);

                                                echo Html::hiddenInput('status_approval_driver_actual-' . $dataStatusApprovalDriverRequire['status_approval_driver_id'], $dataLogStatusApprovalDriver['status_approval_driver_id']);
                                                echo Html::hiddenInput('log_status_approval_driver_actual-' . $dataStatusApprovalDriverRequire['status_approval_driver_id'], $dataLogStatusApprovalDriver['id']);
                                                echo '&nbsp;&nbsp;&nbsp;';
                                            }
                                        } ?>

                                    </div>
								</div>

							<?php
                            endif;
                        endforeach;

                        echo Html::hiddenInput('status_approval_driver_id', null, ['class' => 'status-approval-driver-id']);

                    ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/magnific-popup.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/jquery.magnific-popup.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $(\'[data-toggle="tooltip"]\').tooltip();

    $(".submit-approval-driver").on("click", function() {

        $(".status-approval-driver-id").val($(this).data("status-approval-driver-id"));
        $("#registry-driver-form").trigger("submit");
    });
';

$this->registerJs($jscript); ?>