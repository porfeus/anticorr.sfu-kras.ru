<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%question}}".
 *
 * @property integer $id
 * @property integer $module_id
 * @property integer $theme_id
 * @property string $title
 * @property integer $sort
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $comment_after
 *
 * @property Answer[] $answers
 * @property array $answersList
 * @property Module $module
 * @property Theme $theme
 */
class Question extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%question}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id', 'title'], 'required'],
            [['module_id', 'theme_id', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['title', 'comment_after'], 'string'],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::className(), 'targetAttribute' => ['module_id' => 'id']],
            [['theme_id'], 'exist', 'skipOnError' => true, 'targetClass' => Theme::className(), 'targetAttribute' => ['theme_id' => 'id']],
            ['sort', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module_id' => 'Module ID',
            'theme_id' => 'Theme ID',
            'title' => 'Title',
            'sort' => 'Sort',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'comment_after' => 'Комментарий после теста',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answer::className(), ['question_id' => 'id'])->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTheme()
    {
        return $this->hasOne(Theme::className(), ['id' => 'theme_id']);
    }

    /**
     * @inheritdoc
     * @return QuestionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new QuestionQuery(get_called_class());
    }

    /**
     * @return bool
     */
    public function multipleTrueAnswers()
    {
        return array_reduce($this->answers, function ($count, $model) {
            /** @var Answer $model */
            if ($model->is_true === 1) {
                $count++;
            }
            return $count;
        }, 0) > 1;
    }
    public function getTrueAnswers(){
        $array=array();
        foreach ($this->answers as $answer) {

            if ($answer->is_true==1){
                array_push($array,$answer->id);
            }
        }
        return $array;
    }

    /**
     * @return array
     */
    public function getAnswersList()
    {
        return ArrayHelper::map($this->answers, 'id', 'title');
    }
}
