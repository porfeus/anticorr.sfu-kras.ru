<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 28.07.2017
 * Time: 21:59
 */

namespace app\models;

use yii\helpers\ArrayHelper;
use yii\validators\RequiredValidator;

use yii\base\Model;

class Qa extends Model
{
    public $answers = [];
    /** @var Theme|Module */
    public $model;

    public function rules()
    {
        return [
            ['answers', 'answersValidate']
        ];
    }

    public function answersValidate($attribute)
    {
        foreach ($this->$attribute as $question_id => $userAnswer) {
            $this->answersValidateInternal($question_id, $userAnswer);
        }
    }

    protected function answersValidateInternal($question_id, $userAnswer)
    {
        if (empty($userAnswer)) {
            $this->addError("answers[$question_id]", 'Необходимо указать ответ');
        } else {
            $params = [];
            if ($this->model instanceof Module) {
                $params['module_id'] = $this->model->id;
            } elseif ($this->model instanceof Theme) {
                $params['module_id'] = $this->model->module_id;
                $params['theme_id'] = $this->model->id;
            }
            $userAnswers = is_array($userAnswer) ? $userAnswer : [$userAnswer];
            foreach ($userAnswers as $userAnswerId) {
                $this->_answers[] = new UserAnswer(ArrayHelper::merge([
                    'user_id' => \Yii::$app->user->id,
                    'question_id' => $question_id,
                    'answer_id' => $userAnswerId,
                ], $params));
            }
        }
    }
    
    /** @var array|UserAnswer[] */
    protected $_answers = [];
    
    public function saveResult()
    {
        foreach ($this->_answers as $answer) {
            $answer->save();
        }
    }

    /**
     * @param Module|Theme $model
     * @return Qa
     */
    public static function loadPrevResults($model)
    {
        $self = new self();
        $userAnswers = [];
        foreach ($model->userAnswers as $userAnswer) {
            if ($userAnswer->question->multipleTrueAnswers()) {
                $userAnswers[$userAnswer->question_id][] = $userAnswer->answer_id;
            } else {
                $userAnswers[$userAnswer->question_id] = $userAnswer->answer_id;
            }
            //$self->results[$userAnswer->answer_id] = $userAnswer->answer->is_true;
        }
        $answers = Answer::find()
            ->where(['in', 'question_id', ArrayHelper::map($model->questions, 'id', 'id')])
            ->select(['id', 'is_true'])
            ->asArray()
            ->all();
        $self->results = ArrayHelper::map($answers, 'id', 'is_true');
        $self->answers = $userAnswers;
        return $self;
    }

    public $results = [];
}