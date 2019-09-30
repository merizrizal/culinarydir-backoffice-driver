<?php

use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use sycomponent\Tools;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryDriver */
/* @var $id string */
/* @var $appDriverId string */
/* @var $actid string */
/* @var $logsaid string */
/* @var $form yii\widgets\ActiveForm */

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

$this->title = 'Check & Set ' . \Yii::t('app', 'Driver Attachment') . ' : ' . $model['first_name'];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Approval Driver'), 'url' =>  ['status-driver/' . strtolower($statusApproval) . '-driver']];
$this->params['breadcrumbs'][] = ['label' => $model['first_name'] . ' ' . $model['last_name'], 'url' => ['status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId, 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = 'Check & Set ' . \Yii::t('app', 'Driver Attachment');

echo $ajaxRequest->component();

$btnOk = Html::submitButton('<i class="fa fa-check-circle"></i> OK & Save', ['class' => 'btn btn-success']);
$btnUpdate = Html::a('<i class="fa fa-pencil-alt"></i> Edit', ['registry-driver-approval/update-driver-attachment', 'id' => $id, 'appDriverId' => $appDriverId, 'actid' => $actid, 'logsaid' => $logsaid], ['class' => 'btn btn-primary']);
$btnCancel = '  ' . Html::a('<i class="fa fa-times"></i> Cancel', ['status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId], ['class' => 'btn btn-default']); ?>

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

                        echo Html::hiddenInput('check_set_driver_attachment', true);

                        echo $btnOk, $btnUpdate, $btnCancel; ?>

                        <div class="clearfix" style="margin-top: 15px"></div>

                        <div class="row">
                            <div class="col-xs-12">
                                <h4><strong><?= \Yii::t('app', 'Status') ?></strong> : <?= $model['applicationDriver']['logStatusApprovalDrivers'][0]['statusApprovalDriver']['name'] ?></h4>
                            </div>
                            <div class="col-xs-12">
                                <h4><strong><?= \Yii::t('app', 'User In Charge') ?></strong> : <?= $model['userInCharge']['full_name'] ?></h4>
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

                                                <?= Html::img(\Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_driver_attachment/', $dataDriverAttachments['file_name'], 200, 150), ['style' => 'width: 100%; display: block;']);  ?>

                                                <div class="mask">
                                                    <p>&nbsp;</p>
                                                    <div class="tools tools-bottom">
                                                        <a class="show-image direct" href="<?= \Yii::getAlias('@uploadsUrl') . '/img/registry_driver_attachment/' . $dataDriverAttachments['file_name'] ?>"><i class="fa fa-search"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h5 class="text-center"><strong><?= $dataDriverAttachments['type'] ?></strong></h5>
                                    </div>

                                <?php
                                endforeach;
                            endif; ?>

                        </div>

                        <hr>

                        <?php
                        echo $btnOk, $btnUpdate, $btnCancel;

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
    $(".thumbnail").magnificPopup({
        delegate: "a.show-image",
        type: "image",
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1]
        },
        image: {
            tError: "The image could not be loaded."
        }
    });
';

$this->registerJs($jscript); ?>