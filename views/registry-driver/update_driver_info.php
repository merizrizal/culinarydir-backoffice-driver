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
/* @var $modelPerson core\models\Person */
/* @var $form yii\widgets\ActiveForm */
/* @var $motorBrand array */
/* @var $motorType array */
/* @var $statusApproval string */

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

$this->title = 'Update ' . \Yii::t('app', 'Person As Driver') . ' : ' . $model->first_name . ' ' . $model->last_name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Person As Driver'), 'url' => ['index-' . strtolower($statusApproval)]];
$this->params['breadcrumbs'][] = ['label' => $model->first_name . ' ' . $model->last_name, 'url' => ['view-' . strtolower($statusApproval), 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

echo $ajaxRequest->component(); ?>

<div class="registry-driver-update">
   <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="registry-driver-form">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'registry-driver-form',
                        'action' => ['update-driver-info', 'id' => $model->id, 'statusApproval' => strtolower($statusApproval)],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_content">
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
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-6 mt-30">

                                        <?php
                                        echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                        echo Html::a('<i class="fa fa-times"></i> Cancel', ['view-' . strtolower($statusApproval), 'id' => $model['id']], ['class' => 'btn btn-default']); ?>

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

    $(".checkbox-other-driver").on("ifChecked", function(e) {

        $("#registrydriver-other_driver").removeAttr("disabled");
    });

    $(".checkbox-other-driver").on("ifUnchecked", function(e) {

        $("#registrydriver-other_driver").attr("disabled", "disabled");
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>