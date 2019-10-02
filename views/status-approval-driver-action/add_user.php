<?php

use core\models\City;
use kartik\file\FileInput;
use kartik\number\NumberControl;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model core\models\UserAsDriver */
/* @var $modelUser core\models\User */
/* @var $modelPerson core\models\Person */
/* @var $form yii\widgets\ActiveForm */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'UserAsDriver',
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

$this->title = 'Create ' . \Yii::t('app', 'User As Driver');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Driver'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component(); ?>

<div class="row">
    <div class="col-sm-12">
        <div class="x_panel">
            <div class="user-as-driver-form">

                <?php
                $form = ActiveForm::begin([
                    'id' => 'user-as-driver-form',
                    'action' => $model->isNewRecord ? ['create'] : ['update', 'id' => $model->user_id],
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

                    	<?= $form->field($modelPerson, 'first_name', [
                    	    'parts' => [
                    	        '{inputClass}' => 'col-lg-4'
                    	    ]
                    	])->textInput(['readonly' => true]) ?>

                    	<?= $form->field($modelPerson, 'last_name', [
                    	    'parts' => [
                    	        '{inputClass}' => 'col-lg-4'
                    	    ]
                    	])->textInput(['readonly' => true]) ?>

                    	<?= $form->field($modelPerson, 'phone', [
                    	    'parts' => [
                    	        '{inputClass}' => 'col-lg-4'
                    	    ]
                    	])->widget(MaskedInput::className(), [
                            'options' => ['readonly' => true],
                            'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                        ]) ?>

                        <?= $form->field($modelPerson, 'city_id', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-4'
                            ]
                        ])->dropDownList(
                            ArrayHelper::map(
                                City::find()->orderBy('name')->andWhere(['name' => 'Bandung'])->asArray()->all(),
                                'id',
                                function($data) {

                                    return $data['name'];
                                }
                            ),
                            [
                                'style' => 'width: 100%'
                            ]) ?>

						<?= $form->field($modelPerson, 'email', [
						    'enableAjaxValidation' => true,
						    'parts' => [
						        '{inputClass}' => 'col-lg-4'
						    ]
						])->textInput(['maxlength' => true, 'readonly'=> true]) ?>

                        <?= $form->field($modelUser, 'username', [
                            'enableAjaxValidation' => true,
                            'parts' => [
                                '{inputClass}' => 'col-lg-4'
                            ]
                        ])->textInput(['maxlength' => true, 'readonly' => true]) ?>

                        <?= $form->field($modelUser, 'password', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-4'
                            ]
                        ])->passwordInput(['maxlength' => true, 'readonly' => true]) ?>

                        <?= $form->field($modelUser, 'image')->widget(FileInput::classname(), [
                            'options' => [
                                'accept' => 'image/*'
                            ],
                            'pluginOptions' => [
                                'initialPreview' => [
                                    Html::img(Yii::getAlias('@uploadsUrl') . $modelUser->thumb('/img/user/', 'image', 200, 200), ['class'=>'file-preview-image']),
                                ],
                                'showRemove' => false,
                                'showUpload' => false,
                            ]
                        ]); ?>

                        <?= $form->field($modelUser, 'not_active')->checkbox(['value' => true, 'checked' => true], false) ?>

						<?= $form->field($model, 'total_cash', [
						    'parts' => [
						        '{inputClass}' => 'col-lg-4'
						    ]
						])->widget(NumberControl::className(), [
						    'maskedInputOptions' => Yii::$app->params['maskedInputOptions'],
						]) ?>

                        <?= $form->field($model, 'is_online')->checkbox(['value' => true], false) ?>

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

    $("#person-city_id").select2({
        theme: "krajee",
        placeholder: "",
        minimumResultsForSearch: -1
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>