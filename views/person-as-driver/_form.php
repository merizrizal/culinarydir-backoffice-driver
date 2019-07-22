<?php

use core\models\District;
use core\models\Settings;
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

<?= $ajaxRequest->component() ?>

<div class="row">
    <div class="col-sm-12">
        <div class="x_panel">
            <div class="person-as-driver-form">

                <?php
                $form = ActiveForm::begin([
                    'id' => 'person-as-driver-form',
                    'action' => $model->isNewRecord ? ['create'] : ['update', 'id' => $model->id],
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

                    <div class="x_title">

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6">

                                    <?php
                                    if (!$model->isNewRecord)
                                        echo Html::a('<i class="fa fa-upload"></i> Create', ['create'], ['class' => 'btn btn-success']); ?>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="x_content">

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
                                function($data) {
                                    return $data['name'];
                                }
                            ),
                            [
                                'prompt' => '',
                                'style' => 'width: 100%'
                            ]) ?>

                    	<?php
                        $motorBrandJson = Settings::find()->where(['setting_name' => 'get_motor_brand'])->one()->setting_value;
                        $motorTypeJson = Settings::find()->where(['setting_name' => 'get_motor_type'])->one()->setting_value;
                        $motorBrandArr = json_decode($motorBrandJson, true);
                        $motorTypeArr = json_decode($motorTypeJson, true); ?>

                    	<?= $form->field($model, 'motor_brand')->dropDownList(
                            $motorBrandArr,
                            [
                                'prompt' => '',
                                'style' => 'width: 100%'
                            ]) ?>

                        <?= $form->field($model, 'motor_type')->dropDownList(
                            $motorTypeArr,
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

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-offset-3 col-lg-6">

                                    <?php
                                    $icon = '<i class="fa fa-save"></i> ';
                                    echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                    echo Html::a('<i class="fa fa-times"></i> Cancel', ['index'], ['class' => 'btn btn-default']); ?>

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

<?php

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

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
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>