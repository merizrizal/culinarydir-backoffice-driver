<?php

namespace backoffice\modules\driver\controllers;

use core\models\ApplicationDriver;
use core\models\LogStatusApprovalDriver;
use core\models\LogStatusApprovalDriverAction;
use core\models\RegistryDriver;
use core\models\RegistryDriverAttachment;
use core\models\Settings;
use core\models\StatusApprovalDriver;
use core\models\StatusApprovalDriverAction;
use core\models\search\RegistryDriverSearch;
use Yii;
use sycomponent\AjaxRequest;
use sycomponent\Tools;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * RegistryDriverController implements the CRUD actions for RegistryDriver model.
 */
class RegistryDriverController extends \backoffice\controllers\BaseController
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

    /**
     * Creates a new RegistryDriver model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($save = null)
    {
        $model = new RegistryDriver();
        $modelRegistryDriverAttachment = new RegistryDriverAttachment();
        $modelRegistryDriverAttachment->setScenario(RegistryDriverAttachment::SCENARIO_CREATE);

        if (!empty(($post = \Yii::$app->request->post()))) {

            if ($model->load($post) && $modelRegistryDriverAttachment->load($post)) {

                if (empty($save)) {

                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                } else {

                    $transaction = \Yii::$app->db->beginTransaction();
                    $flag = false;

                    $modelApplicationDriver = new ApplicationDriver();
                    $modelApplicationDriver->user_in_charge = \Yii::$app->user->identity->id;
                    $modelApplicationDriver->counter = 1;

                    if (($flag = $modelApplicationDriver->save())) {

                        $modelLogStatusApprovalDriver = new LogStatusApprovalDriver();
                        $modelLogStatusApprovalDriver->application_driver_id = $modelApplicationDriver->id;
                        $modelLogStatusApprovalDriver->status_approval_driver_id = StatusApprovalDriver::find()->andWhere(['group' => '0'])->asArray()->one()['id'];
                        $modelLogStatusApprovalDriver->is_actual = true;
                        $modelLogStatusApprovalDriver->application_driver_counter = $modelApplicationDriver->counter;
                    }

                    if (($flag = $modelLogStatusApprovalDriver->save())) {

                        $model->application_driver_id = $modelApplicationDriver->id;
                        $model->user_in_charge = $modelApplicationDriver->user_in_charge;
                        $model->application_driver_counter = $modelApplicationDriver->counter;
                    }

                    if (($flag = $model->save())) {

                        $images = Tools::uploadFiles('/img/registry_driver_attachment/', $modelRegistryDriverAttachment, 'file_name', 'registry_driver_id', $model->id, true);

                        if (($flag = count($post['RegistryDriverAttachment']['type']) == count($images))) {

                            foreach ($images as $i => $image) {

                                $newModelRegistryDriverAttachment = new RegistryDriverAttachment();
                                $newModelRegistryDriverAttachment->registry_driver_id = $model->id;
                                $newModelRegistryDriverAttachment->file_name = $image;
                                $newModelRegistryDriverAttachment->type = $post['RegistryDriverAttachment']['type'][$i];

                                if (!($flag = $newModelRegistryDriverAttachment->save())) {

                                    break;
                                }
                            }
                        } else {

                            $modelRegistryDriverAttachment->addError('type', \Yii::t('app', 'Number of files and number of photos does not match.'));
                        }
                    }

                    if ($flag) {

                        \Yii::$app->session->setFlash('status', 'success');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Success'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is success. Data has been saved'));

                        $transaction->commit();

                        return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl(['driver/registry-driver/view-pndg', 'id' => $model->id]));
                    } else {

                        \Yii::$app->session->setFlash('status', 'danger');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Fail'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is fail. Data fail to save'));

                        $transaction->rollback();
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

        return $this->render('create', [
            'model' => $model,
            'modelRegistryDriverAttachment' => $modelRegistryDriverAttachment,
            'motorBrand' => $motorBrand,
            'motorType' => $motorType,
            'attachmentType' => $attachmentType,
        ]);
    }

    public function actionUpdateDriverInfo($id, $save = null, $statusApproval)
    {
        $model = $this->findModel($id);

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
            ->andWhere(['setting_name' => ['motor_brand', 'motor_type']])
            ->asArray()->all();

        $dataArray = [];

        foreach ($modelSettings as $dataSettings) {

            $dataArray[$dataSettings['setting_name']] = $dataSettings['setting_value'];
        }

        $motorBrand = json_decode($dataArray['motor_brand'], true);
        $motorType = json_decode($dataArray['motor_type'], true);

        return $this->render('update_driver_info', [
            'model' => $model,
            'motorBrand' => $motorBrand,
            'motorType' => $motorType,
            'statusApproval' => $statusApproval,
        ]);
    }

    public function actionUpdateDriverAttachment($id, $save = null, $statusApproval)
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

                    $images = Tools::uploadFiles('/img/registry_driver_attachment/', $modelDriverAttachment, 'file_name', 'registry_driver_id', $model->id, true);

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
            'modelDriverAttachment' => $modelDriverAttachment,
            'dataDriverAttachment' => $dataDriverAttachment,
            'attachmentType' => $attachmentType,
            'statusApproval' => $statusApproval,
        ]);
    }

    public function actionResubmit($id, $appDriverId, $appDriverCounter, $statusApproval)
    {
        $modelStatusApprovalDriverAction = StatusApprovalDriverAction::find()
            ->andWhere(['url' => 'status-approval-driver-action/fix-incorrect'])
            ->asArray()->one();

        $modelLogStatusApprovalDriver = LogStatusApprovalDriver::find()
            ->andWhere(['application_driver_id' => $appDriverId])
            ->andWhere(['status_approval_driver_id' => $statusApproval])
            ->andWhere(['is_actual' => true])
            ->andWhere(['application_driver_counter' => $appDriverCounter])
            ->one();

        $transaction = \Yii::$app->db->beginTransaction();
        $flag = true;

        $modelLogStatusApprovalDriver->is_actual = false;

        if (($flag = $modelLogStatusApprovalDriver->save())) {

            $modelLogStatusApprovalActionDriver = new LogStatusApprovalDriverAction();
            $modelLogStatusApprovalActionDriver->log_status_approval_driver_id = $modelLogStatusApprovalDriver['id'];
            $modelLogStatusApprovalActionDriver->status_approval_driver_action_id = $modelStatusApprovalDriverAction['id'];

            if (($flag = $modelLogStatusApprovalActionDriver->save())) {

                $modelLogStatusApprovalDriver = new LogStatusApprovalDriver();
                $modelLogStatusApprovalDriver->application_driver_id = $appDriverId;
                $modelLogStatusApprovalDriver->status_approval_driver_id = 'RSBMT';
                $modelLogStatusApprovalDriver->is_actual = true;
                $modelLogStatusApprovalDriver->application_driver_counter = $appDriverCounter;
            }

            if (($flag = $modelLogStatusApprovalDriver->save())) {

                $flag = $this->run('/driver/status-approval-driver/resubmit', ['appDriverId' => $appDriverId, 'regDriverId' => $id]);
            }
        }

        if ($flag) {

            \Yii::$app->session->setFlash('status', 'success');
            \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
            \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));

            $transaction->commit();

            return AjaxRequest::redirect($this, \Yii::$app->urlManager->createUrl([$this->module->id . '/registry-driver/index-icorct']));
        } else {

            \Yii::$app->session->setFlash('status', 'danger');
            \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
            \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));

            $transaction->rollBack();

            return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl([$this->module->id . '/registry-driver/view-icorct', 'id' => $id]));
        }
    }

    /**
     * Deletes an existing Registry Driver model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $statusApproval)
    {
        if (($model = $this->findModel($id)) !== false) {

            $flag = false;
            $error = '';

            try {
                $flag = $model->delete();
            } catch (\yii\db\Exception $exc) {
                $error = \Yii::$app->params['errMysql'][$exc->errorInfo[1]];
            }
        }

        if ($flag) {

            \Yii::$app->session->setFlash('status', 'success');
            \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Delete Is Success'));
            \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Delete process is success. Data has been deleted'));
        } else {

            \Yii::$app->session->setFlash('status', 'danger');
            \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Delete Is Fail'));
            \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Delete process is fail. Data fail to delete' . $error));
        }

        $return = [];

        $return['url'] = Yii::$app->urlManager->createUrl([$this->module->id . '/registry-driver/index-' . $statusApproval]);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionIndexPndg()
    {
        $actionColumn = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '
                <div class="btn-container hide">
                    <div class="visible-lg visible-md">
                        <div class="btn-group btn-group-md" role="group" style="width: 80px">
                            {view}{delete}
                        </div>
                    </div>
                    <div class="visible-sm visible-xs">
                        <div class="btn-group btn-group-lg" role="group" style="width: 104px">
                            {view}{delete}
                        </div>
                    </div>
                </div>',
            'buttons' => [
                'view' => function ($url, $model, $key) {

                    return Html::a('<i class="fa fa-search-plus"></i>', ['view-pndg', 'id' => $model->id], [
                        'id' => 'view',
                        'class' => 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => 'View',
                    ]);
                },
                'delete' => function ($url, $model, $key) {

                    return Html::a('<i class="fa fa-trash-alt"></i>', ['delete', 'id' => $model->id, 'statusApproval' => 'pndg'], [
                        'id' => 'delete',
                        'class' => 'btn btn-danger',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'data-not-ajax' => 1,
                        'title' => 'Delete',
                        'model-id' => $model->id,
                        'model-name' => $model->first_name,
                    ]);
                },
            ]
        ];

        return $this->index('PNDG', \Yii::t('app', 'Pending Driver'), $actionColumn);
    }

    public function actionIndexIcorct()
    {
        $actionColumn = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '
                <div class="btn-container hide">
                    <div class="visible-lg visible-md">
                        <div class="btn-group btn-group-md" role="group" style="width: 40px">
                            {view}
                        </div>
                    </div>
                    <div class="visible-sm visible-xs">
                        <div class="btn-group btn-group-lg" role="group" style="width: 52px">
                            {view}
                        </div>
                    </div>
                </div>',
            'buttons' => [
                'view' => function ($url, $model, $key) {

                    return Html::a('<i class="fa fa-check"></i>', ['view-icorct', 'id' => $model->id], [
                        'id' => 'view',
                        'class' => 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => 'View',
                    ]);
                },
            ]
        ];

        return $this->index('ICORCT', \Yii::t('app', 'Incorrect Driver'), $actionColumn);
    }

    public function actionIndexRjct()
    {
        $actionColumn = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '
                <div class="btn-container hide">
                    <div class="visible-lg visible-md">
                        <div class="btn-group btn-group-md" role="group" style="width: 40px">
                            {view}
                        </div>
                    </div>
                    <div class="visible-sm visible-xs">
                        <div class="btn-group btn-group-lg" role="group" style="width: 52px">
                            {view}
                        </div>
                    </div>
                </div>',
            'buttons' => [
                'view' => function ($url, $model, $key) {

                    return Html::a('<i class="fa fa-search-plus"></i>', ['view-rjct', 'id' => $model->id], [
                        'id' => 'view',
                        'class' => 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => 'View',
                    ]);
                }
            ]
        ];

        return $this->index('RJCT', \Yii::t('app', 'Reject Driver'), $actionColumn);
    }

    public function actionViewPndg($id)
    {
        $actionButton = [
            'update-driver-info' => function ($model) {

                return Html::a('<i class="fa fa-pencil-alt"></i> Edit Informasi Driver', ['update-driver-info', 'id' => $model['id'], 'statusApproval' => 'pndg'], [
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'tooltip',
                ]);
            },
            'update-driver-attachment' => function ($model) {

                return ' ' . Html::a('<i class="fa fa-pencil-alt"></i> Edit Berkas Driver', ['update-driver-attachment', 'id' => $model['id'], 'statusApproval' => 'pndg'], [
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'tooltip',
                ]);
            },
            'delete' => function ($model) {

                return ' ' . Html::a('<i class="fa fa-trash-alt"></i> Delete', ['delete', 'id' => $model['id'], 'statusApproval' => 'pndg'], [
                    'id' => 'delete',
                    'class' => 'btn btn-danger',
                    'style' => 'color:white',
                    'data-not-ajax' => 1,
                    'model-id' => $model->id,
                    'model-name' => $model->first_name,
                ]);
            },
        ];

        return $this->view($id, 'PNDG', $actionButton);
    }

    public function actionViewIcorct($id)
    {
        $actionButton = [
            'update-driver-info' => function ($model) {

                return Html::a('<i class="fa fa-pencil-alt"></i> Edit Informasi Driver', ['update-driver-info', 'id' => $model['id'], 'statusApproval' => 'icorct'], [
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'tooltip',
                ]);
            },
            'update-driver-attachment' => function ($model) {

                return ' ' . Html::a('<i class="fa fa-pencil-alt"></i> Edit Berkas Driver', ['update-driver-attachment', 'id' => $model['id'], 'statusApproval' => 'icorct'], [
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'tooltip',
                ]);
            },
            'resubmit' => function ($model) {

                return ' ' . Html::a('<i class="fa fa-check"></i> Resubmit', ['resubmit', 'id' => $model['id'], 'appDriverId' => $model['applicationDriver']['id'], 'appDriverCounter' => $model['applicationDriver']['counter'], 'statusApproval' => 'ICORCT'], [
                    'id' => 'resubmit',
                    'class' => 'btn btn-success',
                ]);
            },
        ];

        return $this->view($id, 'ICORCT', $actionButton);
    }

    public function actionViewRjct($id)
    {
        return $this->view($id, 'RJCT');
    }

    protected function findModel($id)
    {
        if (($model = RegistryDriver::findOne($id)) !== null) {

            return $model;
        } else {

            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Lists all RegistryDriver models.
     * @return mixed
     */
    private function index($statusApproval, $title, $actionColumn)
    {
        $searchModel = new RegistryDriverSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['log_status_approval_driver.status_approval_driver_id' => $statusApproval])
            ->andWhere(['log_status_approval_driver.is_actual' => 1])
            ->andWhere('registry_driver.application_driver_counter = application_driver.counter')
            ->distinct();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => $title,
            'statusApproval' => $statusApproval,
            'actionColumn' => $actionColumn,
        ]);
    }

    private function view($id, $statusApproval, $actionButton = null)
    {
        $model = RegistryDriver::find()
            ->joinWith([
                'registryDriverAttachments',
                'applicationDriver.logStatusApprovalDrivers.statusApprovalDriver'
            ])
            ->andWhere(['registry_driver.id' => $id])
            ->one();

        return $this->render('view', [
            'model' => $model,
            'statusApproval' => $statusApproval,
            'actionButton' => $actionButton,
        ]);
    }
}
