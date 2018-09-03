<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27.07.2017
 * Time: 22:13
 */

namespace app\modules\api\controllers;

use app\modules\api\models\Module;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class ModuleController extends ActiveController
{
    public $modelClass = 'app\modules\api\models\Module';

    //public function verbs()
    //{
    //    return ArrayHelper::merge(parent::verbs(), [
    //        'set-order' => ['PUT', 'PATCH'],
    //    ]);
    //}

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function actionOrder()
    {
        if ($data = \Yii::$app->request->bodyParams) {
            if (is_array($data)) {
                foreach ($data as $key => $item) {
                    if (!isset($item['id']) || !is_numeric($key)) {
                        continue;
                    }
                    Module::updateAll(['sort' => $key], ['id' => $item['id']]);
                }
            }
        }
        return \Yii::$app->request->bodyParams;
    }

    /**
     * @return ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        return new ActiveDataProvider([
            'query' => Module::find()->sort(),
            'pagination' => false,
        ]);
    }
}