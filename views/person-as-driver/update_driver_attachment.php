<?php

use kartik\file\FileInput;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use sycomponent\Tools;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model core\models\PersonAsDriver */
/* @var $modelDriverAttachment core\models\DriverAttachment */
/* @var $form yii\widgets\ActiveForm */
/* @var $dataDriverAttachment array */
/* @var $attachmentType array */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'DriverAttachment',
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

$this->title = 'Update ' . \Yii::t('app', 'Driver Attachment') . ' : ' . $model->person->first_name . ' ' . $model->person->last_name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Person As Driver'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->person->first_name . ' ' . $model->person->last_name, 'url' => ['view', 'id' => $model->person->id]];
$this->params['breadcrumbs'][] = 'Update';

echo $ajaxRequest->component(); ?>

<div class="driver-attachment-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="driver-attachment-form">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'driver-attachment-form',
                        'action' => ['update-driver-attachment', 'id' => $model->person_id],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_title">
                            <h4><?= \Yii::t('app', 'Driver Attachment') ?></h4>
                        </div>

                       	<div class="x_content">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-2">
                                        <div class="row">

                                            <?php
                                            foreach ($dataDriverAttachment as $driverAttachment): ?>

    	                                       	<div class="col-xs-6 col-sm-4">
                                                    <div class="thumbnail">
                                                        <div class="image view view-first">

                                                            <?= Html::img(\Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/driver_attachment/', $driverAttachment['file_name'], 200, 150), ['style' => 'width: 100%; display: block;']);  ?>

                                                            <div class="mask">
                                                                <p>&nbsp;</p>
                                                                <div class="tools tools-bottom">
                                                                    <a class="show-image direct" href="<?= \Yii::getAlias('@uploadsUrl') . '/img/driver_attachment/' . $driverAttachment['file_name'] ?>"><i class="fa fa-search"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-10">
                                                    		<div class="row">
                                                				<div class="col-xs-12">

                                                        			<?= Html::dropDownList('type['. $driverAttachment['id'] .']', !empty($driverAttachment['type']) ? $driverAttachment['type'] : null, $attachmentType, [
                                                        			    'prompt' => '',
                                                        			    'class' => 'attachment-type',
                                                        			    'style' => 'width:100%'
                                                        			]) ?>

                                                        		</div>
                                                        	</div>

                                                    		<div class="row mt-10">
                                                				<div class="col-xs-12">
                                                            		<?= Html::checkbox('DriverAttachmentDelete[]', false, ['label' => 'Delete', 'value' => $driverAttachment['id']]) ?>
                                                            	</div>
                                                        	</div>
                                                        </div>
                                                    </div>
                                                </div>

    										<?php
    									    endforeach; ?>

                                        </div>
                                    </div>
                                </div>
							</div>

							<div class="form-group">
								<div class="row mb-20">
            						<div class="col-xs-12 col-sm-2">
            							<label><?= Yii::t('app', 'Attachment Type') ?></label>
            						</div>
            						<div class="col-xs-12 col-sm-10">

										<?= $form->field($modelDriverAttachment, 'type')->dropDownList($attachmentType, [
                                            'multiple' => 'multiple',
                                            'prompt' => '',
                                            'style' => 'width: 100%',
                                        ]) ?>

            						</div>
            					</div>

                                <div class="row">
                                    <div class="col-xs-12 col-sm-2">
                                        <?= Html::label(\Yii::t('app', 'Driver Attachment')) ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-10">

                                        <?= $form->field($modelDriverAttachment, 'file_name[]')->widget(FileInput::classname(), [
                                            'options' => [
                                                'accept' => 'image/*',
                                                'multiple' => true,
                                            ],
                                            'pluginOptions' => [
                                                'showRemove' => true,
                                                'showUpload' => false,
                                            ]
                                        ]); ?>

                                    </div>
                                </div>
                            </div>

							<div class="row">
                                <div class="col-xs-12">

                                    <?php
                                    echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                    echo Html::a('<i class="fa fa-times"></i> Cancel', ['view', 'id' => $model['person_id']], ['class' => 'btn btn-default']); ?>

                                </div>
                            </div>
                        </div>

                    <?php
                    ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/magnific-popup.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/jquery.magnific-popup.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $(".attachment-type").select2({
        theme: "krajee",
        minimumResultsForSearch: Infinity,
        placeholder: "' . \Yii::t('app', 'Attachment Type') . '"
    });

    $("#driverattachment-type").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . \Yii::t('app', 'Attachment Type') . '"
    });

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

    $("form#driver-attachment-form").on("beforeSubmit", function(event) {

        var driverAttachmentType = $("#driverattachment-type").parent();

        if (driverAttachmentType.hasClass("has-error")) {

            driverAttachmentType.removeClass("has-error");
            $(this).find(".help-block").remove();
            $(this).trigger("submit");
        }
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>