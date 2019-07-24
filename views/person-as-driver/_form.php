<?php

use core\models\District;
use kartik\datetime\DateTimePicker;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model core\models\PersonAsDriver */
/* @var $form yii\widgets\ActiveForm */
/* @var $modelPerson core\models\Person */
/* @var $modelDriverCriteria core\models\DriverCriteria */
/* @var $modelDriverAttachment core\models\DriverAttachment */
/* @var $motorBrand array */
/* @var $motorType array */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);


$ajaxRequest = new AjaxRequest([
    'modelClass' => 'PersonAsDriver',
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
} ?>

<?= $ajaxRequest->component();

$jscript = '
    $("#wizard-create-application").steps({
        titleTemplate:
            "<span class=\"number\">" +
                "#index#" +
            "</span>" +
            "<span class=\"desc\">" +
                "#title#" +
            "</span>",
        onInit: function(event, currentIndex) {

            $("#wizard-create-application.wizard > .actions ul li a").addClass("btn btn-primary");
            $("#wizard-create-application.wizard > .actions").removeClass("actions").addClass("actionBar");
            $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#previous\"]").addClass("buttonDisabled");
        },
        onStepChanged: function(event, currentIndex, priorIndex) {

            if (priorIndex == 0) {

                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#previous\"]").removeClass("buttonDisabled");
            } else if (currentIndex == 0) {

                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#previous\"]").addClass("buttonDisabled");
            }

            var lastCount = $("#wizard-create-application.wizard > .steps").find("li").length - 1;

            if (currentIndex == lastCount) {

                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#next\"]").addClass("buttonDisabled");
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#next\"]").parent().hide();

                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#finish\"]").parent().show();
            } else if (priorIndex == lastCount) {

                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#next\"]").removeClass("buttonDisabled");
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#next\"]").parent().show();

                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#finish\"]").parent().hide();
            }
        },
        onFinished: function(event, currentIndex) {

            $("#person-as-driver-form").trigger("submit");
        },
        labels: {
            finish: "<i class=\"fa fa-save\"></i> Save",
            next: "<i class=\"fa fa-angle-double-right\"></i> Next",
            previous: "<i class=\"fa fa-angle-double-left\"></i> Previous"
        }
    });
';

$this->registerJs($jscript); ?>

<div class="driver-create">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="person-as-driver-form">
                	<div class="x_title"></div>
                	<div class="x_content">

                	<?php
                    $form = ActiveForm::begin([
                        'id' => 'person-as-driver-form',
                        'action' => $model->isNewRecord ? ['create'] : ['update', 'id' => $model->person_id],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'parts' => [
                                '{inputClass}' => 'col-lg-6'
                            ],
                            'template' => '
                                <div class="row">
                                    <div class="col-lg-3">
                                        {label}
                                    </div>
                                    <div class="{inputClass}">
                                        {input}
                                    </div>
                                    <div class="col-lg-3">
                                        {error}
                                    </div>
                                </div>',
                        ]
                    ]); ?>

                        <div id="wizard-create-application">
	                    	<h1><?= Yii::t('app', 'Identitas Driver') ?></h1>
							<div>

								<?= $form->field($modelPerson, 'first_name')->textInput(['maxlength' => true]) ?>

		                        <?= $form->field($modelPerson, 'last_name')->textInput(['maxlength' => true]) ?>

								<?= $form->field($modelPerson, 'email')->textInput(['maxlength' => true]) ?>

                                <?= $form->field($model, 'no_ktp')->textInput(['maxlength' => true]) ?>

                            	<?= $form->field($model, 'no_sim')->textInput(['maxlength' => true]) ?>

                            	<?= $form->field($model, 'date_birth', [
                                    'parts' => [
                                        '{inputClass}' => 'col-lg-4'
                                    ],
                                ])->widget(DateTimePicker::className(), [
                                    'pluginOptions' => Yii::$app->params['datepickerOptions'],
                                ]) ?>

                               	<?= $form->field($modelPerson, 'phone')->widget(MaskedInput::className(), [
                                    'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                    'options' => [
                                        'placeholder' => Yii::t('app', 'Phone'),
                                        'class' => 'form-control'
                                    ]
                                ]) ?>

                            	<?= $form->field($model, 'district_id')->dropDownList(
                                    ArrayHelper::map(
                                        District::find()->orderBy('name')->asArray()->all(),
                                        'id',
                                        function($data)
                                        {
                                            return $data['name'];
                                        }
                                    ),
                                    [
                                        'prompt' => '',
                                        'style' => 'width: 100%'
                                    ]) ?>

                            	<?= $form->field($model, 'motor_brand')->dropDownList(
                                    $motorBrand,
                                    [
                                        'prompt' => '',
                                        'style' => 'width: 100%'
                                    ]) ?>

                                <?= $form->field($model, 'motor_type')->dropDownList(
                                    $motorType,
                                    [
                                        'prompt' => '',
                                        'style' => 'width: 100%'
                                    ]) ?>

                                <?= $form->field($model, 'number_plate')->textInput(['maxlength' => true]) ?>

                                <?= $form->field($model, 'stnk_expired', [
                                    'parts' => [
                                        '{inputClass}' => 'col-lg-4'
                                    ],
                                ])->widget(DateTimePicker::className(), [
                                    'pluginOptions' => Yii::$app->params['datepickerOptions'],
                                ]) ?>

                                <?= $form->field($model, 'emergency_contact_name')->textInput(['maxlength' => true]) ?>

                                <?= $form->field($model, 'emergency_contact_phone')->widget(MaskedInput::className(), [
                                    'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                    'options' => [
                                        'placeholder' => Yii::t('app', 'Emergency Contact Phone'),
                                        'class' => 'form-control'
                                    ]
                                ]) ?>

                                <?= $form->field($model, 'emergency_contact_address')->textarea(['rows' => 3, 'placeholder' => Yii::t('app', 'Address')]) ?>

        						<div class="col-lg-offset-3 col-lg-6 mb-20">

            						<?= Html::checkbox('other_driver', false, [
                                        'label' => Yii::t('app', 'Other Driver ?'),
                                        'class' => 'checkbox-other-driver'
                                    ]); ?>

                                    <?= $form->field($model, 'other_driver', ['template' => '{input}' ])->textInput(['maxlength' => true, 'disabled' => 'disabled'])->label(false) ?>

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
$this->registerCssFile(Yii::$app->urlManager->baseUrl . '/media/plugins/jquery-steps/demo/css/jquery.steps.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile(Yii::$app->urlManager->baseUrl . '/media/css/jquery.steps.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$cssscript = '
    .wizard > .content > .body ul > li {
        display: block;
    }
';

$this->registerCss($cssscript);

$this->registerJsFile(Yii::$app->urlManager->baseUrl . '/media/plugins/jquery-steps/build/jquery.steps.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $("#personasdriver-district_id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'District') . '"
    });

    $("#personasdriver-motor_brand").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Merek Motor') . '"
    });

    $("#personasdriver-motor_type").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Tipe Motor') . '"
    });

    $(".checkbox-other-driver").on("ifChecked", function(e) {

        $("#personasdriver-other_driver").removeAttr("disabled");
    });

    $(".checkbox-other-driver").on("ifUnchecked", function(e) {

        $("#personasdriver-other_driver").attr("disabled", "disabled");
    });

    $("#driver-criteria-4").on("ifChecked", function(e) {

        alert("nomor 1 terceklis")
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>