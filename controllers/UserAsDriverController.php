<?php

namespace backoffice\modules\driver\controllers;

use sycomponent\Tools;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use core\models\UserAsDriver;
use core\models\search\UserAsDriverSearch;
use core\models\User;
use core\models\Person;
use core\models\UserLevel;
use core\models\UserPerson;

/**
 * UserAsDriverController implements the CRUD actions for UserAsDriver model.
 */
class UserAsDriverController extends \backoffice\controllers\BaseController
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
     * Lists all UserAsDriver models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserAsDriverSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserAsDriver model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserAsDriver model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($save = null)
    {
        $render = 'create';

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
                    $modelUser->password = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

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

        return $this->render($render, [
            'model' => $model,
            'modelUser' => $modelUser,
            'modelPerson' => $modelPerson
        ]);
    }

    /**
     * Updates an existing UserAsDriver model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id, $save = null)
    {
        $model = $this->findModel($id);
        $modelUser = $model->user;
        $modelPerson = $model->user->userPerson->person;

        if (!empty(($post = \Yii::$app->request->post()))) {

            if ($modelUser->load($post) && $modelPerson->load($post) && $model->load($post)) {

                if (empty($save)) {

                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($modelUser);
                } else {

                    $transaction = \Yii::$app->db->beginTransaction();
                    $flag = false;

                    if (!($modelUser->image = Tools::uploadFile('/img/user/', $modelUser, 'image', 'username', $modelUser->username))) {

                        $modelUser->image = $modelUser->oldAttributes['image'];
                    }

                    $modelUser->full_name = $modelPerson->first_name . ' ' . $modelPerson->last_name;

                    if (($flag = $modelUser->save())) {

                        $modelPerson->email = $post['User']['email'];

                        if (($flag = $modelPerson->save())) {

                            $flag = $model->save();
                        }
                    }

                    if ($flag) {

                        \Yii::$app->session->setFlash('status', 'success');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Success'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is success. Data has been saved'));

                        $transaction->commit();
                    } else {

                        \Yii::$app->session->setFlash('status', 'danger');
                        \Yii::$app->session->setFlash('message1', \Yii::t('app', 'Update Data Is Fail'));
                        \Yii::$app->session->setFlash('message2', \Yii::t('app', 'Update data process is fail. Data fail to save'));

                        $transaction->rollback();
                    }
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelUser' => $modelUser,
            'modelPerson' => $modelPerson
        ]);
    }

    /**
     * Finds the UserAsDriver model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UserAsDriver the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserAsDriver::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
