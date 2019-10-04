<?php

namespace backoffice\modules\driver\controllers;

use core\models\LogStatusApprovalDriverAction;
use core\models\Person;
use core\models\RegistryDriver;
use core\models\User;
use core\models\UserAkses;
use core\models\UserAksesAppModule;
use core\models\UserAsDriver;
use core\models\UserLevel;
use core\models\UserPerson;
use core\models\UserRole;
use sycomponent\AjaxRequest;
use sycomponent\Tools;
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

    public function actionAddUser($id, $appDriverId, $logsaid, $actid, $save = null, $statusApproval)
    {
        $model = new UserAsDriver();
        $modelUser = new User();
        $modelPerson = Person::find()
            ->joinWith(['personAsDriver.applicationDriver'])
            ->andWhere(['application_driver.id' => $appDriverId])
            ->one();

        $usernameByEmail = explode("@", $modelPerson['email']);

        $modelUser->username = $usernameByEmail[0];
        $modelUser->password = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

        $passwordTemp = $modelUser['password'];

        if (!empty(($post = \Yii::$app->request->post()))) {

            if ($modelUser->load($post) && $modelPerson->load($post) && $model->load($post)) {

                if (empty($save)) {

                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($modelUser);
                } else {

                    $transaction = \Yii::$app->db->beginTransaction();
                    $flag = false;

                    $modelLogStatusApprovalDriverAction = new LogStatusApprovalDriverAction();
                    $modelLogStatusApprovalDriverAction->log_status_approval_driver_id = $logsaid;
                    $modelLogStatusApprovalDriverAction->status_approval_driver_action_id = $actid;

                    if (($flag = $modelLogStatusApprovalDriverAction->save())) {

                        $modelUser->email = $post['Person']['email'];
                        $modelUser->full_name = $post['Person']['first_name'] . ' ' . $post['Person']['last_name'];
                        $modelUser->setPassword($passwordTemp);
                        $modelUser->image = Tools::uploadFile('/img/user/', $modelUser, 'image', 'username', $modelUser->username);

                        $flag = $modelUser->save();
                    }

                    if ($flag) {

                        $userLevel = UserLevel::find()
                            ->andWhere(['nama_level' => 'Driver'])
                            ->asArray()->one();

                        $modelUserRole = new UserRole();
                        $modelUserRole->user_id = $modelUser->id;
                        $modelUserRole->user_level_id = $userLevel['id'];
                        $modelUserRole->unique_id = $modelUser->id . '-' . $userLevel['id'];
                        $modelUserRole->is_active = true;

                        $flag = $modelUserRole->save();
                    }

                    if ($flag) {

                        $modelUserAkses = UserAkses::find()
                            ->andWhere(['user_level_id' => $modelUserRole->user_level_id])
                            ->asArray()->all();

                        foreach ($modelUserAkses as $dataUserAkses) {

                            $modelUserAksesAppModule = new UserAksesAppModule();
                            $modelUserAksesAppModule->unique_id = $modelUser->id . '-' . $dataUserAkses['user_app_module_id'];
                            $modelUserAksesAppModule->user_id = $modelUser->id;
                            $modelUserAksesAppModule->user_app_module_id = $dataUserAkses['user_app_module_id'];
                            $modelUserAksesAppModule->is_active = $dataUserAkses['is_active'];
                            $modelUserAksesAppModule->used_by_user_role = [$modelUserRole->unique_id];

                            if (!($flag = $modelUserAksesAppModule->save())) {

                                break;
                            }
                        }
                    }

                    if ($flag) {

                        $modelPerson->city_id = $post['Person']['city_id'];

                        $flag = $modelPerson->save();
                    }

                    if ($flag) {

                        $model->user_id = $modelUser->id;
                        $model->person_as_driver_id = $modelPerson['personAsDriver']['person_id'];

                        $flag = $model->save();
                    }

                    if ($flag) {

                        $modelUserPerson = new UserPerson();
                        $modelUserPerson->user_id = $modelUser->id;
                        $modelUserPerson->person_id = $modelPerson->id;

                        $flag = $modelUserPerson->save();
                    }

                    if ($flag) {

                        $randomString = \Yii::$app->security->generateRandomString();
                        $randomStringHalf = substr($randomString, 16);
                        $modelUser->not_active = true;
                        $modelUser->account_activation_token = substr($randomString, 0, 15) . $modelUser->id . $randomStringHalf . '_' . time();

                        if (($flag = $modelUser->save())) {

                            \Yii::$app->mailer->compose(['html' => 'account_activation'], [
                                'email' => $post['Person']['email'],
                                'full_name' => $post['Person']['first_name'] . ' ' . $post['Person']['last_name'],
                                'userToken' => $modelUser->account_activation_token,
                                'isFromBackoffice' => true,
                                'password' => $passwordTemp,
                            ])
                            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' Support'])
                            ->setTo($post['Person']['email'])
                            ->setSubject(\Yii::$app->name . ' Account Activation')
                            ->send();
                        }
                    }

                    if ($flag) {

                        \Yii::$app->session->setFlash('status', 'success');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Success'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is success. Data has been saved'));

                        $transaction->commit();

                        return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl(['/driver/status-driver/view-driver', 'id' => $id, 'appDriverId' => $appDriverId, 'statusApproval' => $statusApproval]));
                    } else {

                        \Yii::$app->session->setFlash('status', 'danger');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Fail'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is fail. Data fail to save'));

                        $transaction->rollback();
                    }
                }
            }
        }

        return $this->render('add_user', [
            'model' => $model,
            'modelUser' => $modelUser,
            'modelPerson' => $modelPerson,
            'id' => $id,
            'appDriverId' => $appDriverId,
            'actid' => $actid,
            'logsaid' => $logsaid,
            'statusApproval' => $statusApproval,
        ]);
    }

    public function actionActivateAccount($token)
    {
        $modelUser = User::find()
        ->andWhere(['account_activation_token' => $token])
        ->andWhere(['not_active' => true])
        ->one();

        if (!empty($modelUser)) {

            $modelUser->not_active = false;

            if ($modelUser->save()) {

                return $this->render('message', [
                    'fullname' => $modelUser['full_name'],
                    'title' => \Yii::t('app', 'Your Account Has Been Activated'),
                    'messages' => \Yii::t('app', 'Please login with your Email / Username by clicking the button below.'),
                    'links' => ['name' => \Yii::t('app', 'Login to {app}', ['app' => \Yii::$app->name]), 'url' => ['site/login']],
                ]);
            } else {

                \Yii::$app->session->setFlash('message', [
                    'type' => 'danger',
                    'delay' => 1000,
                    'icon' => 'aicon aicon-icon-info',
                    'message' => 'Gagal Aktivasi Akun Anda',
                    'title' => 'Gagal Aktivasi',
                ]);

                return $this->redirect(['register']);
            }
        } else {

            return $this->redirect(['login']);
        }
    }

}