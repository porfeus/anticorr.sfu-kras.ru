<?php

namespace app\controllers;

use app\models\ReviewForm;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\widgets\ActiveForm;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            //'verbs' => [
            //    'class' => VerbFilter::className(),
            //    'actions' => [
            //        'logout' => ['post'],
            //    ],
            //],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //if (!Yii::$app->user->isGuest) {
        //    return $this->redirect(['/course/index']);
        //}
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        $request = \Yii::$app->request;
        if ($model->load(Yii::$app->request->post())) {
            if ($request->isAjax) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                $model->login();
            }
        }
        return $this->goBack();
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    //public function actionContact()
    //{
    //    $model = new ContactForm();
    //    if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
    //        Yii::$app->session->setFlash('contactFormSubmitted');
    //
    //        return $this->refresh();
    //    }
    //    return $this->render('contact', [
    //        'model' => $model,
    //    ]);
    //}

    /**
     * Displays about page.
     *
     * @return string
     */
    //public function actionAbout()
    //{
    //    return $this->render('about');
    //}
    public function actionContacts()
    {
        return $this->render('contacts');
    }
    
    public function actionReview()
    {
        if (Yii::$app->user->identity->review === User::REVIEW_SEND) {
            return $this->render('review-successfully');
        }
        $model = new ReviewForm();
        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            return $this->redirect(['review']);
        }
        return $this->render('review', [
            'model' => $model,
        ]);
    }
}
