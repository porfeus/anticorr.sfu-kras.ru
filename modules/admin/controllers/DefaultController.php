<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\BaseController;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends BaseController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCourse()
    {
        return $this->render('course');
    }
}
