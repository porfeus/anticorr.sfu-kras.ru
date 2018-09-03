<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Module;
use app\modules\api\models\Theme;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use app\modules\api\models\Question;
use yii\web\NotFoundHttpException;

/**
 * Class QaController
 * @package app\modules\api\controllers
 */
class QaController extends Controller
{
    protected function checkExistBaseModels($module_id, $theme_id = null)
    {
        if (!empty($theme_id)) {
            if (!Theme::find()->where(['id' => $theme_id])->exists()) {
                throw new NotFoundHttpException('Тема не найдена, возможно она была удалена');
            }
        }
        if (!Module::find()->where(['id' => $module_id])->exists()) {
            throw new NotFoundHttpException('Модуль не найден, возможно он был удален');
        }
    }

    public function actionGet($module_id, $theme_id = null)
    {
        $this->checkExistBaseModels($module_id, $theme_id);
        return Question::find()->where([
            'module_id' => $module_id,
            'theme_id' => !empty($theme_id) ? $theme_id : null,
        ])->all();
    }

    public function actionUpdate($module_id, $theme_id = null)
    {
        $condition = [
            'module_id' => $module_id,
            'theme_id' => !empty($theme_id) ? $theme_id : null,
        ];
        $old = Question::find()->where($condition)->all(); /** @var array|Question[] $models */
        $old = ArrayHelper::map($old, 'id', function($model) {
            return $model;
        });
        //var_dump($old);
        if ($data = \Yii::$app->request->bodyParams) {
            if (is_array($data)) {
                $valid = true;
                foreach ($data as $sort => $item) {
                    $model = null;
                    if (isset($item['id']) && isset($old[$item['id']])) {
                        $model = $old[$item['id']];
                        unset($old[$item['id']]);
                    }
                    $model = isset($model) ? $model : new Question();
                    $model->load($item, '');
                    $model->sort = $sort;
                    $model->module_id = $module_id;
                    $model->theme_id = $theme_id;
                    $valid = $valid && $model->validate();
                    $models[] = $model;
                }
                if (!$valid) {
                    \Yii::$app->response->statusCode = 422;
                    return array_map(function ($model) {
                        /** @var Question $model */
                        return $model->getFirstErrors();
                    }, $models);
                } else {
                    foreach ($models as $model) {
                        $model->save(false);
                    }
                }

            }
        }
        Question::deleteAll(['in', 'id', array_keys($old)]);
        return array_keys($old);
    }
    public function actionUpload($module_id = null, $theme_id = null){
        $condition = [
            'module_id' => $_POST["module_id"],
            'theme_id' => !empty($_POST["theme_id"]) ? $_POST["theme_id"] : null,
            'id' => $_POST["id"],
        ];
        $qa = Question::find()->where($condition)->one();
        $dir = "/home/admin/web/corrupt.sfu-kras.ru/public_html/web";
        if(isset($_POST["delete_file"])){
            $delete_file = $dir . $qa->file;
            unlink($delete_file);
            $qa->file = "";
            $qa->save();
            return "delete_file";
        }
        $path_dir = "uploads/question/".$qa->id."/" ;
        if(!file_exists($path_dir)){
            mkdir($path_dir, 0777, true);
        }
        $path_file = $path_dir . basename($_FILES['testfile']['name']);
        $path_file = str_replace("\s","",$path_file);
        move_uploaded_file($_FILES['testfile']['tmp_name'], $path_file);
        if($qa->file != "") {
            $delete_file = $dir . $qa->file;
            unlink($delete_file);
        }
        $qa->file = "/".$path_file;
        $qa->save();
        return "succes";
    }
}