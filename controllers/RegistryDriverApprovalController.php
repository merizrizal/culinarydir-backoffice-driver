<?php

namespace backoffice\modules\driver\controllers;

use core\models\RegistryDriver;
use core\models\RegistryDriverAttachment;
use core\models\Settings;
use sycomponent\Tools;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;

class RegistryDriverApprovalController extends \backoffice\controllers\BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [

                    ],
                ],
            ]);
    }

    public function actionUpdateDriverInfo($id, $save = null, $appDriverId, $actid, $logsaid)
    {
        $model = RegistryDriver::findOne($id);

        if (!empty(($post = \Yii::$app->request->post()))) {

            if ($model->load($post)) {

                if (empty($save)) {

                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                } else {

                    if ($model->save()) {

                        \Yii::$app->session->setFlash('status', 'success');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));
                    } else {

                        \Yii::$app->session->setFlash('status', 'danger');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));
                    }
                }
            }
        }

        $modelSettings = Settings::find()
            ->andWhere(['setting_name' => ['motor_brand', 'motor_type', 'attachment_type']])
            ->asArray()->all();

        $dataArray = [];

        foreach ($modelSettings as $dataSettings) {

            $dataArray[$dataSettings['setting_name']] = $dataSettings['setting_value'];
        }

        $motorBrand = json_decode($dataArray['motor_brand'], true);
        $motorType = json_decode($dataArray['motor_type'], true);
        $attachmentType = json_decode($dataArray['attachment_type'], true);

        return $this->render('update_driver_info', [
            'model' => $model,
            'id' => $id,
            'appDriverId' => $appDriverId,
            'actid' => $actid,
            'logsaid' => $logsaid,
            'motorBrand' => $motorBrand,
            'motorType' => $motorType,
            'attachmentType' => $attachmentType,
        ]);
    }

    public function actionUpdateDriverAttachment($id, $save = null, $appDriverId, $actid, $logsaid)
    {
        $model = RegistryDriver::find()
            ->joinWith(['registryDriverAttachments'])
            ->andWhere(['registry_driver.id' => $id])
            ->one();

        $modelDriverAttachment = new RegistryDriverAttachment();
        $dataDriverAttachment = [];
        $newDataDriverAttachment = [];
        $deletedDriverAttachmentId = [];

        if (!empty(($post = \Yii::$app->request->post()))) {

            if ($modelDriverAttachment->load($post)) {

                if (!empty($save)) {

                    $transaction = \Yii::$app->db->beginTransaction();
                    $flag = true;

                    $images = Tools::uploadFiles('/img/registry_driver_attachment/', $modelDriverAttachment, 'file_name', 'registry_driver_id', '', true);

                    if (!empty($images) || !empty($post['RegistryDriverAttachment']['type'])) {

                        if (empty($post['RegistryDriverAttachment']['type'])) {

                            $post['RegistryDriverAttachment']['type'] = [];
                        }

                        if (($flag = count($images) == count($post['RegistryDriverAttachment']['type']))) {

                            foreach ($images as $i => $image) {

                                $newModelDriverAttachment = new RegistryDriverAttachment();
                                $newModelDriverAttachment->registry_driver_id = $model->id;
                                $newModelDriverAttachment->file_name = $image;
                                $newModelDriverAttachment->type = $post['RegistryDriverAttachment']['type'][$i];

                                if (!($flag = $newModelDriverAttachment->save())) {

                                    break;
                                } else {

                                    array_push($newDataDriverAttachment, $newModelDriverAttachment->toArray());
                                }
                            }
                        } else {

                            $modelDriverAttachment->addError('type', \Yii::t('app', 'Number of files and number of photos does not match.'));
                        }
                    }

                    if ($flag) {

                        if (!empty($post['DriverAttachmentDelete'])) {

                            if (($flag = RegistryDriverAttachment::deleteAll(['id' => $post['DriverAttachmentDelete']]))) {

                                $deletedDriverAttachmentId = $post['DriverAttachmentDelete'];
                            }
                        }
                    }

                    if ($flag) {

                        foreach ($model->registryDriverAttachments as $existModelDriverAttachment) {

                            $existModelDriverAttachment->type = $post['type'][$existModelDriverAttachment->id];

                            if (!($flag = $existModelDriverAttachment->save())) {

                                break;
                            }
                        }
                    }

                    if ($flag) {

                        \Yii::$app->session->setFlash('status', 'success');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));

                        $transaction->commit();

                        $modelDriverAttachment = new RegistryDriverAttachment();
                    } else {

                        \Yii::$app->session->setFlash('status', 'danger');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));

                        $transaction->rollBack();
                    }
                }
            }
        }

        foreach ($model['registryDriverAttachments'] as $valueDriverAttachment) {

            $deleted = false;

            foreach ($deletedDriverAttachmentId as $DriverAttachmentId) {

                if ($DriverAttachmentId == $valueDriverAttachment['id']) {

                    $deleted = true;
                    break;
                }
            }

            if (!$deleted) {

                array_push($dataDriverAttachment, $valueDriverAttachment);
            }
        }

        if (!empty($newDataDriverAttachment)) {

            $dataDriverAttachment = ArrayHelper::merge($dataDriverAttachment, $newDataDriverAttachment);
        }

        $modelSettings = Settings::find()
            ->andWhere(['setting_name' => 'attachment_type'])
            ->asArray()->one();

        $attachmentType = json_decode($modelSettings['setting_value'], true);

        return $this->render('update_driver_attachment', [
            'model' => $model,
            'id' => $id,
            'appDriverId' => $appDriverId,
            'actid' => $actid,
            'logsaid' => $logsaid,
            'modelDriverAttachment' => $modelDriverAttachment,
            'dataDriverAttachment' => $dataDriverAttachment,
            'attachmentType' => $attachmentType,
        ]);
    }
}