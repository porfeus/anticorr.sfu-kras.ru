<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 28.07.2017
 * Time: 8:08
 */

namespace app\modules\api\models;


class Answer extends \app\models\Answer
{
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string'],
            //['is_true', 'integer'],
            ['sort', 'default', 'value' => 0],
            ['is_true', 'default', 'value' => 0],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'question_id',
            'title',
            'is_true',
            'sort',
        ];
    }
}