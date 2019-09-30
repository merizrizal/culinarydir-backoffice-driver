<?php

namespace backoffice\modules\driver\controllers;

use core\models\LogStatusApprovalDriverAction;
use core\models\Person;
use core\models\RegistryDriver;
use core\models\User;
use core\models\UserAsDriver;
use core\models\UserLevel;
use core\models\UserPerson;
use sycomponent\AjaxRequest;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

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

    public function actionFixIncorrect($id, $appDriverId, $logsaid, $actid, $statusApproval)
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

        return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl(['/driver/status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId, 'statusApproval' => $statusApproval]));
    }

    public function actionCheckSetDriverInfo($id, $appDriverId, $logsaid, $actid, $statusApproval)
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

        if (!empty(\Yii::$app->request->post('check_set_driver_info'))) {

            $modelLogStatusApprovalDriverAction = new LogStatusApprovalDriverAction();
            $modelLogStatusApprovalDriverAction->log_status_approval_driver_id = $logsaid;
            $modelLogStatusApprovalDriverAction->status_approval_driver_action_id = $actid;

            if ($modelLogStatusApprovalDriverAction->save()) {

                \Yii::$app->session->setFlash('status', 'success');
                \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
                \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));

                return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl(['/driver/status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId, 'statusApproval' => $statusApproval]));
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
            'statusApproval' => $statusApproval,
        ]);
    }

    public function actionCheckSetDriverAttachment($id, $appDriverId, $logsaid, $actid, $statusApproval)
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

        if (!empty(\Yii::$app->request->post('check_set_driver_attachment'))) {

            $modelLogStatusApprovalDriverAction = new LogStatusApprovalDriverAction();
            $modelLogStatusApprovalDriverAction->log_status_approval_driver_id = $logsaid;
            $modelLogStatusApprovalDriverAction->status_approval_driver_action_id = $actid;

            if ($modelLogStatusApprovalDriverAction->save()) {

                \Yii::$app->session->setFlash('status', 'success');
                \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
                \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));

                return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl(['/driver/status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId, 'statusApproval' => $statusApproval]));
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
            'statusApproval' => $statusApproval,
        ]);
    }

    public function actionAddUser($id, $appDriverId, $logsaid, $actid, $save = null)
    {
        $model = new UserAsDriver();
        $modelUser = new User();
        $modelPerson = new Person();

        if (!empty(($post = \Yii::$app->request->post()))) {

            if ($modelUser->load($post) && $modelPerson->load($post) && $model->load($post)) {

                if (empty($save)) {

                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($modelUser);
                } else {

                    $transaction = \Yii::$app->db->beginTransaction();
                    $flag = false;

                    $userLevel = UserLevel::find()
                        ->andWhere(['nama_level' => 'Driver'])
                        ->asArray()->one();

                    $modelUser->user_level_id = $userLevel['id'];
                    $modelUser->full_name = $post['Person']['first_name'] . ' ' . $post['Person']['last_name'];
                    $modelUser->setPassword($post['User']['password']);

                    if (($flag = $modelUser->save())) {

                        $modelPerson->email = $post['User']['email'];

                        if (($flag = $modelPerson->save())) {

                            $model->user_id = $modelUser->id;

                            if (($flag = $model->save())) {

                                $modelUserPerson = new UserPerson();
                                $modelUserPerson->user_id = $modelUser->id;
                                $modelUserPerson->person_id = $modelPerson->id;

                                $flag = $modelUserPerson->save();
                            }
                        }
                    }

                    if ($flag) {

                        \Yii::$app->session->setFlash('status', 'success');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Success'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is success. Data has been saved'));

                        $transaction->commit();

                        $render = 'view';
                    } else {

                        $model->setIsNewRecord(true);

                        \Yii::$app->session->setFlash('status', 'danger');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Fail'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is fail. Data fail to save'));

                        $transaction->rollback();
                    }
                }
            }
        }
    }
}