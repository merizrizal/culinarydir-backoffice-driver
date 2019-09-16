<?php

namespace backoffice\modules\driver\controllers;

use backoffice\controllers\BaseController;
use core\models\RegistryDriver;
use core\models\RegistryDriverAttachment;
use core\models\Settings;
use core\models\search\RegistryDriverSearch;
use sycomponent\Tools;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * RegistryDriverController implements the CRUD actions for RegistryDriver model.
 */
class RegistryDriverController extends BaseController
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
        $render = 'create';

        $model = new RegistryDriver();
        $modelRegistryDriverAttachment = new RegistryDriverAttachment();
        $modelRegistryDriverAttachment->setScenario(RegistryDriverAttachment::SCENARIO_CREATE);

        if (!empty(($post = \Yii::$app->request->post()))) {

            if ($model->load($post) && $modelRegistryDriverAttachment->load($post)) {

                if (!empty($save)) {

                    $transaction = \Yii::$app->db->beginTransaction();
                    $flag = false;

                    if (($flag = $model->save())) {

                        $images = Tools::uploadFiles('/img/driver_attachment/', $modelRegistryDriverAttachment, 'file_name', 'registry_driver_id', '', true);

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

                        $render = 'view';

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

        return $this->render($render, [
            'model' => $model,
            'modelRegistryDriverAttachment' => $modelRegistryDriverAttachment,
            'motorBrand' => $motorBrand,
            'motorType' => $motorType,
            'attachmentType' => $attachmentType,
        ]);
    }

    /**
     * Updates an existing RegistryDriver model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id, $save = null)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->request->post())) {

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

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionIndexPndg()
    {
        $actionColumn = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '
                <div class="btn-container hide">
                    <div class="visible-lg visible-md">
                        <div class="btn-group btn-group-md" role="group" style="width: 80px">
                            {view}
                        </div>
                    </div>
                    <div class="visible-sm visible-xs">
                        <div class="btn-group btn-group-lg" role="group" style="width: 104px">
                            {view}
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
                        <div class="btn-group btn-group-md" role="group" style="width: 80px">
                            {view}
                        </div>
                    </div>
                    <div class="visible-sm visible-xs">
                        <div class="btn-group btn-group-lg" role="group" style="width: 104px">
                            {view}
                        </div>
                    </div>
                </div>',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-search-plus"></i>', ['view-icorct', 'id' => $model->id], [
                        'id' => 'view',
                        'class' => 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => 'View',
                    ]);
                },
            ]
        ];

        return $this->index('ICORCT', \Yii::t('app', 'Incorect Driver'), $actionColumn);
    }

    public function actionIndexRjct()
    {
        $actionColumn = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '
                <div class="btn-container hide">
                    <div class="visible-lg visible-md">
                        <div class="btn-group btn-group-md" role="group" style="width: 80px">
                            {view}
                        </div>
                    </div>
                    <div class="visible-sm visible-xs">
                        <div class="btn-group btn-group-lg" role="group" style="width: 104px">
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
                },
            ]
        ];

        return $this->index('RJCT', \Yii::t('app', 'Reject Driver'), $actionColumn);
    }

    public function actionViewPndg($id)
    {
        $actionButton = [
            'update-driver-info' => function ($model) {
                return Html::a('<i class="fa fa-pencil-alt"></i> Edit Informasi Driver', ['update-driver-info', 'id' => $model['id']], [
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'tooltip',
                ]);
            },
            'update-driver-attachment' => function ($model) {
                return Html::a('<i class="fa fa-pencil-alt"></i> Edit Berkas Driver', ['update-driver-attachment', 'id' => $model['id']], [
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'tooltip',
                ]);
            }
        ];

        return $this->view($id, 'PNDG', $actionButton);
    }

    public function actionViewIcorct($id)
    {
        $actionButton = [
            'update-driver-info' => function ($model) {
                return Html::a('<i class="fa fa-pencil-alt"></i> Edit Informasi Driver', ['update-driver-info', 'id' => $model['id']], [
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'tooltip',
                ]);
            },
            'update-driver-attachment' => function ($model) {
                return Html::a('<i class="fa fa-pencil-alt"></i> Edit Berkas Driver', ['update-driver-attachment', 'id' => $model['id']], [
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'tooltip',
                ]);
            }
        ];

        return $this->view($id, 'ICORCT', $actionButton);
    }

    public function actionViewRjct($id)
    {
        $actionButton = [
            'update-driver-info' => function ($model) {
                return Html::a('<i class="fa fa-pencil-alt"></i> Edit Informasi Driver', ['update-driver-info', 'id' => $model['id']], [
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'tooltip',
                ]);
            },
            'update-driver-attachment' => function ($model) {
                return Html::a('<i class="fa fa-pencil-alt"></i> Edit Berkas Driver', ['update-driver-attachment', 'id' => $model['id']], [
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'tooltip',
                ]);
            }
        ];

        return $this->view($id, 'RJCT', $actionButton);
    }

    /**
     * Lists all RegistryDriver models.
     * @return mixed
     */
    private function index($statusApproval, $title, $actionColumn)
    {
        $searchModel = new RegistryDriverSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => $title,
            'statusApproval' => $statusApproval,
            'actionColumn' => $actionColumn,
        ]);
    }

    private function view($id, $statusApproval, $actionButton)
    {
        $model = RegistryDriver::find()
            ->joinWith([
                'registryDriverAttachments'
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
