<?php

use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model core\models\DriverCriteria */
/* @var $form yii\widgets\ActiveForm */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'DriverCriteria',
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
            <div class="driver-criteria-form">

                <?php
                $form = ActiveForm::begin([
                    'id' => 'driver-criteria-form',
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
						<div>
							<div class="row">
                                <div class="col-xs-12">
                                    <h4><strong><?= Yii::t('app', 'Driver Criteria') ?></strong></h4>
                                </div>
                            </div>
                			<hr>

								<?= $form->field($model, 'criteria_name[driver]', [
                                    'template' => '
                                        <div class="row">
                                            <div class="col-sm-12">
                                                {input}
                                                {error}
                                            </div>
                                        </div>
                                    ',
                                ])->checkboxList(
                                    $driverCriteria, [
                                    'item' => function ($index, $label, $name, $checked, $value)
                                    {
                                        return '
                                            <div class="col-xs-12 col-sm-4">
                                                <label>'.
                                                Html::checkbox($name, $checked, ['value' => $label, 'id' => 'driver-criteria-'. $value]) . ' ' . $label . '
                                                </label>
                                            </div>';
                                    }
                                ]) ?>

                            <hr>
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4><strong><?= Yii::t('app', 'Motor Criteria') ?></strong></h4>
                                </div>
                            </div>
                			<hr>

								<?= $form->field($model, 'criteria_name[motor]', [
                                    'template' => '
                                        <div class="row">
                                            <div class="col-sm-12">
                                                {input}
                                                {error}
                                            </div>
                                        </div>
                                    ',
                                ])->checkboxList(
                                    $motorCriteria, [
                                    'item' => function ($index, $label, $name, $checked, $value)
                                    {
                                        return '
                                            <div class="col-xs-12 col-sm-4">
                                                <label>'.
                                                Html::checkbox($name, $checked, ['value' => $label, 'id' => 'motor-criteria-'. $value]) . ' ' . $label . '
                                                </label>
                                            </div>';
                                    }
                                ]) ?>

							<hr>
						</div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-offset-0 col-lg-5">

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
