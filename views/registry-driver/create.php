<?php

use core\models\District;
use kartik\datetime\DateTimePicker;
use kartik\file\FileInput;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryDriver */
/* @var $modelRegistryDriverAttachment core\models\RegistryDriverAttachment */
/* @var $motorBrand array */
/* @var $motorType array */
/* @var $attachmentType array */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

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

$this->title = \Yii::t('app', 'Registry Driver');
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component();

$jscript = '
    $("#wizard-create-data-driver").steps({
        titleTemplate:
            "<span class=\"number\">" +
                "#index#" +
            "</span>" +
            "<span class=\"desc\">" +
                "#title#" +
            "</span>",
        onInit: function(event, currentIndex) {

            $("#wizard-create-data-driver.wizard > .actions ul li a").addClass("btn btn-primary");
            $("#wizard-create-data-driver.wizard > .actions").removeClass("actions").addClass("actionBar");
            $("#wizard-create-data-driver.wizard > .actionBar").find("a[href=\"#previous\"]").addClass("buttonDisabled");
        },
        onStepChanged: function(event, currentIndex, priorIndex) {

            if (priorIndex == 0) {

                $("#wizard-create-data-driver.wizard > .actionBar").find("a[href=\"#previous\"]").removeClass("buttonDisabled");
            } else if (currentIndex == 0) {

                $("#wizard-create-data-driver.wizard > .actionBar").find("a[href=\"#previous\"]").addClass("buttonDisabled");
            }

            var lastCount = $("#wizard-create-data-driver.wizard > .steps").find("li").length - 1;

            if (currentIndex == lastCount) {

                $("#wizard-create-data-driver.wizard > .actionBar").find("a[href=\"#next\"]").addClass("buttonDisabled");
                $("#wizard-create-data-driver.wizard > .actionBar").find("a[href=\"#next\"]").parent().hide();

                $("#wizard-create-data-driver.wizard > .actionBar").find("a[href=\"#finish\"]").parent().show();
            } else if (priorIndex == lastCount) {

                $("#wizard-create-data-driver.wizard > .actionBar").find("a[href=\"#next\"]").removeClass("buttonDisabled");
                $("#wizard-create-data-driver.wizard > .actionBar").find("a[href=\"#next\"]").parent().show();

                $("#wizard-create-data-driver.wizard > .actionBar").find("a[href=\"#finish\"]").parent().hide();
            }
        },
        onFinished: function(event, currentIndex) {

            $("#registry-driver-form").trigger("submit");
        },
        labels: {
            finish: "<i class=\"fa fa-save\"></i> Save",
            next: "<i class=\"fa fa-angle-double-right\"></i> Next",
            previous: "<i class=\"fa fa-angle-double-left\"></i> Previous"
        }
    });
';

$this->registerJs($jscript); ?>

<div class="registry-driver-create">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="registry-driver-form">

                	<?php
                    $form = ActiveForm::begin([
                        'id' => 'registry-driver-form',
                        'action' => ['create'],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

    					<div class="x_title"></div>

                    	<div class="x_content">
                            <div id="wizard-create-data-driver">
    	                    	<h1><?= \Yii::t('app', 'Identitas Driver') ?></h1>
    							<div>
        							<div class="row">
        								<div class="col-xs-12 col-sm-6">
        									<?= $form->field($model, 'first_name')->textInput(['maxlength' => true, 'placeholder' => \Yii::t('app', 'First Name')]) ?>
        								</div>
        								<div class="col-xs-12 col-sm-6">
        									<?= $form->field($model, 'last_name')->textInput(['maxlength' => true, 'placeholder' => \Yii::t('app', 'Last Name')]) ?>
        								</div>
        							</div>

    								<div class="row">
    									<div class="col-xs-12 col-sm-4">
    										<?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => \Yii::t('app', 'Email')]) ?>
    									</div>
    									<div class="col-xs-12 col-sm-4">

    										<?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
                                                'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                                'options' => [
                                                    'placeholder' => \Yii::t('app', 'Phone'),
                                                    'class' => 'form-control'
                                                ]
                                            ]) ?>

    									</div>
    									<div class="col-xs-12 col-sm-4">

    										<?= $form->field($model, 'date_birth', [
                                                'parts' => [
                                                    '{inputClass}' => 'col-lg-4'
                                                ],
                                            ])->widget(DateTimePicker::className(), [
                                                'pluginOptions' => \Yii::$app->params['datepickerOptions'],
                                                'options' => [
                                                    'placeholder' => \Yii::t('app', 'Tanggal Lahir')
                                                ],
                                            ]) ?>

    									</div>
    								</div>

    								<div class="row">
    									<div class="col-xs-12 col-sm-4">
    										<?= $form->field($model, 'no_ktp')->textInput(['maxlength' => true, 'placeholder' => \Yii::t('app', 'Nomor KTP')]) ?>
    									</div>
    									<div class="col-xs-12 col-sm-4">
    										<?= $form->field($model, 'no_sim')->textInput(['maxlength' => true, 'placeholder' => \Yii::t('app', 'Nomor SIM')]) ?>
    									</div>
    									<div class="col-xs-12 col-sm-4">

    										<?= $form->field($model, 'district_id')->dropDownList(
                                                ArrayHelper::map(
                                                    District::find()->orderBy('name')->asArray()->all(),
                                                    'id',
                                                    function($data) {

                                                        return $data['name'];
                                                    }
                                                ),
                                                [
                                                    'prompt' => '',
                                                    'style' => 'width: 100%'
                                                ]) ?>

    									</div>
    								</div>

    								<div class="row">
    									<div class="col-xs-12 col-sm-2">

    										<?= $form->field($model, 'motor_brand')->dropDownList($motorBrand, [
    										    'prompt' => '',
    										    'style' => 'width: 100%'
    										]) ?>

    									</div>
    									<div class="col-xs-12 col-sm-2">

    										<?= $form->field($model, 'motor_type')->dropDownList($motorType, [
    										    'prompt' => '',
    										    'style' => 'width: 100%'
    										]) ?>

    									</div>
    									<div class="col-xs-12 col-sm-4">
    										<?= $form->field($model, 'number_plate')->textInput(['maxlength' => true, 'placeholder' => \Yii::t('app', 'Number Plate')]) ?>
    									</div>
    									<div class="col-xs-12 col-sm-4">

    										<?= $form->field($model, 'stnk_expired', [
                                                'parts' => [
                                                    '{inputClass}' => 'col-lg-4'
                                                ],
                                            ])->widget(DateTimePicker::className(), [
                                                'pluginOptions' => \Yii::$app->params['datepickerOptions'],
                                                'options' => [
                                                    'placeholder' => \Yii::t('app', 'Stnk Expired')
                                                ],
                                            ]) ?>

    									</div>
    								</div>

    								<div class="row">
    									<div class="col-xs-12 col-sm-4">
    										<?= $form->field($model, 'emergency_contact_name')->textInput(['maxlength' => true, 'placeholder' => \Yii::t('app', 'Emergency Contact Name')]) ?>
    									</div>
    									<div class="col-xs-12 col-sm-4">

    										<?= $form->field($model, 'emergency_contact_phone')->widget(MaskedInput::className(), [
                                                'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                                'options' => [
                                                    'placeholder' => \Yii::t('app', 'Emergency Contact Phone'),
                                                    'class' => 'form-control'
                                                ]
                                            ]) ?>

    									</div>
    									<div class="col-xs-12 col-sm-4">
    										<?= $form->field($model, 'emergency_contact_address')->textarea(['rows' => 3, 'placeholder' => \Yii::t('app', 'Emergency Contact Address')]) ?>
    									</div>
    								</div>

            						<div class="row">
                						<div class="col-xs-12 col-sm-5">

            								<?= Html::checkbox('other_driver', false, [
                                                'label' => \Yii::t('app', 'Other Driver ?'),
                                                'class' => 'checkbox-other-driver'
                                            ]); ?>

                                        	<?= $form->field($model, 'other_driver')->textInput(['maxlength' => true, 'disabled' => 'disabled'])->label(false) ?>

                						</div>
                						<div class="col-sm-offset-1 col-xs-12 col-sm-5">

                							<?= $form->field($model, 'is_criteria_passed')->checkbox([false,
                							    'label' => \Yii::t('app', 'Is Criteria Passed')
                							]); ?>

                						</div>
            						</div>
    							</div>

            					<h1><?= \Yii::t('app', 'Driver Attachment') ?></h1>
            					<div>
                					<div class="row mb-20">
                						<div class="col-lg-2">
                							<label><?= \Yii::t('app', 'Attachment Type') ?></label>
                						</div>
                						<div class="col-lg-8">

    										<?= $form->field($modelRegistryDriverAttachment, 'type')->dropDownList($attachmentType, [
                                                'multiple' => 'multiple',
                                                'prompt' => '',
                                                'style' => 'width: 100%',
                                            ]) ?>

                						</div>
                					</div>
                					<div class="row">
                						<div class="col-lg-2">
                							<?= Html::label(\Yii::t('app', 'Photo')) ?>
                						</div>
                						<div class="col-lg-8">

    										<?= $form->field($modelRegistryDriverAttachment, 'file_name[]')->widget(FileInput::classname(), [
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
$this->registerCssFile(\Yii::$app->urlManager->baseUrl . '/media/plugins/jquery-steps/demo/css/jquery.steps.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile(\Yii::$app->urlManager->baseUrl . '/media/css/jquery.steps.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$cssscript = '
    .wizard > .content > .body ul > li {
        display: block;
    }
';

$this->registerCss($cssscript);

$this->registerJsFile(\Yii::$app->urlManager->baseUrl . '/media/plugins/jquery-steps/build/jquery.steps.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $("#registrydriver-district_id").select2({
        theme: "krajee",
        placeholder: "' . \Yii::t('app', 'District') . '"
    });

    $("#registrydriver-motor_brand").select2({
        theme: "krajee",
        placeholder: "' . \Yii::t('app', 'Merek Motor') . '"
    });

    $("#registrydriver-motor_type").select2({
        theme: "krajee",
        placeholder: "' . \Yii::t('app', 'Tipe Motor') . '"
    });

    $("#registrydriverattachment-type").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . \Yii::t('app', 'Berkas') . '"
    });

    $(".checkbox-other-driver").on("ifChecked", function(e) {

        $("#registrydriver-other_driver").removeAttr("disabled");
    });

    $(".checkbox-other-driver").on("ifUnchecked", function(e) {

        $("#registrydriver-other_driver").attr("disabled", "disabled");
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>