<?php

namespace backoffice\modules\driver\controllers;

use core\models\DriverAttachment;
use core\models\Person;
use core\models\PersonAsDriver;
use core\models\Settings;
use core\models\search\PersonAsDriverSearch;
use sycomponent\Tools;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * PersonAsDriverController implements the CRUD actions for PersonAsDriver model.
 */
class PersonAsDriverController extends \backoffice\controllers\BaseController
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
     * Lists all PersonAsDriver models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PersonAsDriverSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionView($id)
    {
        $model = PersonAsDriver::find()
            ->joinWith([
                'person',
                'district',
                'driverAttachments'
            ])
            ->andWhere(['person_as_driver.person_id' => $id])
            ->asArray()->one();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new PersonAsDriver model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($save = null)
    {
        $render = 'create';

        $model = new PersonAsDriver();
        $modelPerson = new Person();
        $modelDriverAttachment = new DriverAttachment();
        $modelDriverAttachment->setScenario(DriverAttachment::SCENARIO_CREATE);

        if ($model->load(($post = \Yii::$app->request->post())) && $modelPerson->load($post)) {

            if (!empty($save)) {

                $transaction = \Yii::$app->db->beginTransaction();
                $flag = false;

                if (($flag = $modelPerson->save())) {

                    $model->person_id = $modelPerson->id;

                    $flag = $model->save();
                }

                if ($flag) {

                    $newModelDriverAttachment = new DriverAttachment();

                    if ($newModelDriverAttachment->load($post)) {

                        $images = Tools::uploadFiles('/img/driver_attachment/', $newModelDriverAttachment, 'file_name', 'person_as_driver_id', '', true);

                        foreach ($images as $i => $image) {

                            $newModelDriverAttachment = new DriverAttachment();
                            $newModelDriverAttachment->person_as_driver_id = $modelPerson->id;
                            $newModelDriverAttachment->file_name = $image;
                            $newModelDriverAttachment->type = $post['DriverAttachment']['type'][$i];

                            if (!($flag = $newModelDriverAttachment->save())) {

                                break;
                            }
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

                    \Yii::$app->session->setFlash('status', 'danger');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Fail'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is fail. Data fail to save'));

                    $transaction->rollBack();
                }
            }
        }

        $dataJson = Settings::find()
            ->andWhere(['setting_name' => ['motor_brand', 'motor_type', 'attachment_type']])
            ->asArray()->all();

        $dataArray = [];

        foreach ($dataJson as $a) {

            $dataArray[$a['setting_name']] = $a['setting_value'];
        }

        $motorBrand = json_decode($dataArray['motor_brand'], true);
        $motorType = json_decode($dataArray['motor_type'], true);
        $attachmentType = json_decode($dataArray['attachment_type'], true);

        return $this->render($render, [
            'model' => $model,
            'modelPerson' => $modelPerson,
            'modelDriverAttachment' => $modelDriverAttachment,
            'motorBrand' => $motorBrand,
            'motorType' => $motorType,
            'attachmentType' => $attachmentType,
        ]);
    }

    /**
     * Updates an existing PersonAsDriver model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdateDriverInfo($id, $save = null)
    {
        $render = 'update_driver_info';

        $model = PersonAsDriver::find()
            ->joinWith([
                'person',
            ])
            ->andWhere(['person_as_driver.person_id' => $id])
            ->one();

        $modelPerson = $model->person;

        if ($model->load(\Yii::$app->request->post()) && $modelPerson->load(\Yii::$app->request->post())) {

            $transaction = \Yii::$app->db->beginTransaction();
            $flag = false;

            if (!empty($save)) {

                if (($flag = $model->save())) {

                    $flag = $modelPerson->save();
                }

                if ($flag) {

                    \Yii::$app->session->setFlash('status', 'success');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));

                    $transaction->commit();

                    $render = 'view';
                } else {

                    \Yii::$app->session->setFlash('status', 'danger');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));

                    $transaction->rollBack();
                }
            }
        }

        $dataJson = Settings::find()
            ->andWhere(['setting_name' => ['motor_brand', 'motor_type']])
            ->asArray()->all();

        $dataArray = [];

        foreach ($dataJson as $a) {

            $dataArray[$a['setting_name']] = $a['setting_value'];
        }

        $motorBrand = json_decode($dataArray['motor_brand'], true);
        $motorType = json_decode($dataArray['motor_type'], true);

        return $this->render($render, [
            'model' => $model,
            'modelPerson' => $modelPerson,
            'motorBrand' => $motorBrand,
            'motorType' => $motorType,
        ]);
    }

    public function actionUpdateDriverAttachment($id, $save = null)
    {
        $model = PersonAsDriver::find()
            ->joinWith([
                'driverAttachments',
            ])
            ->andWhere(['person_as_driver.person_id' => $id])
            ->one();

        $modelDriverAttachment = new DriverAttachment();
        $dataDriverAttachment = [];
        $newDataDriverAttachment = [];
        $deletedDriverAttachmentId = [];

        if (!empty(($post = \Yii::$app->request->post()))) {

            if (!empty($save)) {

                $transaction = \Yii::$app->db->beginTransaction();
                $flag = true;

                if (!empty($post['DriverAttachmentDelete'])) {

                    if(($flag = DriverAttachment::deleteAll(['id' => $post['DriverAttachmentDelete']]))) {

                        $deletedDriverAttachmentId = $post['DriverAttachmentDelete'];
                    }
                }

                if ($flag) {

                    $newModelDriverAttachment = new DriverAttachment();

                    if ($newModelDriverAttachment->load($post)) {

                        $images = Tools::uploadFiles('/img/driver_attachment/', $newModelDriverAttachment, 'file_name', 'person_as_driver_id', '', true);

                        foreach ($images as $i => $image) {

                            $newModelDriverAttachment = new DriverAttachment();
                            $newModelDriverAttachment->person_as_driver_id = $model->person_id;
                            $newModelDriverAttachment->file_name = $image;
                            $newModelDriverAttachment->type = !empty($post['DriverAttachment']['type'][$i]) ? $post['DriverAttachment']['type'][$i] : null;

                            if (!($flag = $newModelDriverAttachment->save())) {

                                break;
                            } else {

                                array_push($newDataDriverAttachment, $newModelDriverAttachment->toArray());
                            }
                        }
                    }
                }

                if ($flag) {

                    foreach ($model->driverAttachments as $existModelDriverAttachment) {

                        $existModelDriverAttachment->type = $post['type'][$existModelDriverAttachment->id];

                        if (!($flag = $existModelDriverAttachment->save())) {

                            break;
                        }
                    }
                }

                if ($flag) {

                    \Yii::$app->session->setFlash('status', 'success');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Success'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is success. Data has been saved'));

                    $transaction->commit();
                } else {

                    \Yii::$app->session->setFlash('status', 'danger');
                    \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Create Data Is Fail'));
                    \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Create data process is fail. Data fail to save'));

                    $transaction->rollBack();
                }
            }
        }

        foreach ($model['driverAttachments'] as $valueDriverAttachment) {

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

        $dataJson = Settings::find()
            ->andWhere(['setting_name' => ['attachment_type']])->asArray()->all();

        $dataArray = [];

        foreach ($dataJson as $a) {

            $dataArray[$a['setting_name']] = $a['setting_value'];
        }

        $attachmentType = json_decode($dataArray['attachment_type'], true);

        return $this->render('update_attachment', [
            'model' => $model,
            'modelDriverAttachment' => $modelDriverAttachment,
            'dataDriverAttachment' => $dataDriverAttachment,
            'attachmentType' => $attachmentType,
        ]);
    }
}
