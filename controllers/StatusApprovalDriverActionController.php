<?php

namespace backoffice\modules\driver\controllers;

use core\models\LogStatusApprovalDriverAction;
use core\models\RegistryDriver;
use sycomponent\AjaxRequest;
use yii\filters\VerbFilter;

/**
 * StatusApprovalDriverActionController implements the CRUD actions for RegistryDriver model.
 */
class StatusApprovalDriverActionController extends \backoffice\controllers\BaseController
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
                        'delete' => ['POST'],
                    ],
                ],
            ]);
    }

    public function actionFixIncorrect($id, $appDriverId, $logsaid, $actid)
    {
        $modelLogStatusApprovalDriverAction = new LogStatusApprovalDriverAction();
        $modelLogStatusApprovalDriverAction->log_status_approval_driver_id = $logsaid;
        $modelLogStatusApprovalDriverAction->status_approval_driver_action_id = $actid;

        if ($modelLogStatusApprovalDriverAction->save()) {

            \Yii::$app->session->setFlash('status', 'success');
            \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
            \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));
        } else {

            \Yii::$app->session->setFlash('status', 'danger');
            \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
            \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));
        }

        return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl(['/driver/status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId]));
    }

    public function actionCheckSetDriverInfo($id, $appDriverId, $logsaid, $actid)
    {
        $model = RegistryDriver::find()
            ->joinWith([
                'district',
                'userInCharge',
                'applicationDriver',
                'applicationDriver.logStatusApprovalDrivers.statusApprovalDriver',
            ])
            ->andWhere(['registry_driver.id' => $id])
            ->asArray()->one();

        if ((\Yii::$app->request->post()) && !empty(\Yii::$app->request->post('check_set_driver_info'))) {

            $modelLogStatusApprovalDriverAction = new LogStatusApprovalDriverAction();
            $modelLogStatusApprovalDriverAction->log_status_approval_driver_id = $logsaid;
            $modelLogStatusApprovalDriverAction->status_approval_driver_action_id = $actid;

            if ($modelLogStatusApprovalDriverAction->save()) {

                \Yii::$app->session->setFlash('status', 'success');
                \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
                \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));

                return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl(['/driver/status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId]));
            } else {

                \Yii::$app->session->setFlash('status', 'danger');
                \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
                \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));
            }
        }

        return $this->render('check_set_driver_info', [
            'model' => $model,
            'id' => $id,
            'appDriverId' => $appDriverId,
            'actid' => $actid,
            'logsaid' => $logsaid,
        ]);
    }

    public function actionCheckSetDriverAttachment($id, $appDriverId, $logsaid, $actid)
    {
        $model = RegistryDriver::find()
            ->joinWith([
                'userInCharge',
                'applicationDriver',
                'applicationDriver.logStatusApprovalDrivers.statusApprovalDriver',
                'registryDriverAttachments'
            ])
            ->andWhere(['registry_driver.id' => $id])
            ->asArray()->one();

        if ((\Yii::$app->request->post()) && !empty(\Yii::$app->request->post('check_set_driver_attachment'))) {

            $modelLogStatusApprovalDriverAction = new LogStatusApprovalDriverAction();
            $modelLogStatusApprovalDriverAction->log_status_approval_driver_id = $logsaid;
            $modelLogStatusApprovalDriverAction->status_approval_driver_action_id = $actid;

            if ($modelLogStatusApprovalDriverAction->save()) {

                \Yii::$app->session->setFlash('status', 'success');
                \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
                \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));

                return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl(['/driver/status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId]));
            } else {

                \Yii::$app->session->setFlash('status', 'danger');
                \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
                \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));
            }
        }

        return $this->render('check_set_driver_attachment', [
            'model' => $model,
            'id' => $id,
            'appDriverId' => $appDriverId,
            'actid' => $actid,
            'logsaid' => $logsaid,
        ]);
    }
}