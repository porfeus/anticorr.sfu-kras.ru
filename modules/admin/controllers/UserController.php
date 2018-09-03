<?php

namespace app\modules\admin\controllers;

use app\models\Module;
use app\models\Qa;
use app\models\Theme;
use app\modules\admin\components\BaseController;
use Yii;
use app\modules\admin\models\User;
use app\modules\admin\models\UserSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $admins = \Yii::$app->authManager->getUserIdsByRole('admin');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['not in', 'user.id', $admins]);
        $adminsDataProvider = new ActiveDataProvider([
            'query' => User::find()->where(['in', 'id', $admins]),
            'sort' => false,
            'pagination' => false,
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'adminsDataProvider' => $adminsDataProvider,
        ]);
    }

    /**
     * Displays a single User model
     * @param $id
     * @param null $module_id
     * @param null $theme_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id, $module_id = null, $theme_id = null, $give_try = null)
    {
        $model = $this->findModel($id);
        Module::$userId = $id;
        Theme::$userId = $id;
        $module = !empty($module_id) ? Module::findOne((int) $module_id) : null;
        $theme = !empty($theme_id) ? Theme::findOne((int) $theme_id) : null;
        $qaBaseModel = $theme ? $theme : $module;
        if ($give_try == 1 && !$qaBaseModel->hasTry()) {
            $qaBaseModel->decrTry();
        }

        $modules = Module::find()->sort()->all();
        return $this->render('view', [
            'model' => $model,
            'modules' => $modules,
            'qaBaseModel' => $qaBaseModel,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = User::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionReReview($id)
    {
        $model = $this->findModel($id);
        $model->review = User::REVIEW_AWAIT;
        $model->save(false, ['review']);
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
