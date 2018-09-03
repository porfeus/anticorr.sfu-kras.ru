<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_answer}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $module_id
 * @property integer $theme_id
 * @property integer $question_id
 * @property integer $answer_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Answer $answer
 * @property Module $module
 * @property Question $question
 * @property Theme $theme
 * @property User $user
 */
class UserAnswer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_answer}}';
    }

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
            [['user_id', 'module_id', 'question_id', 'answer_id'], 'required'],
            [['user_id', 'module_id', 'theme_id', 'question_id', 'answer_id', 'created_at', 'updated_at'], 'integer'],
            [['answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Answer::className(), 'targetAttribute' => ['answer_id' => 'id']],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::className(), 'targetAttribute' => ['module_id' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
            [['theme_id'], 'exist', 'skipOnError' => true, 'targetClass' => Theme::className(), 'targetAttribute' => ['theme_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'module_id' => 'Module ID',
            'theme_id' => 'Theme ID',
            'question_id' => 'Question ID',
            'answer_id' => 'Answer ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(Answer::className(), ['id' => 'answer_id']);
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
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTheme()
    {
        return $this->hasOne(Theme::className(), ['id' => 'theme_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return UserAnswerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserAnswerQuery(get_called_class());
    }
    public static function isTrue($usAnswers,$trueAnswers){
        $result=array_intersect($usAnswers, $trueAnswers);

        if(count($result)==count($trueAnswers) &&(count($usAnswers)==count($trueAnswers))){
            return 1;
        }
        elseif(count($result)==0){
            return 0;
        }
        else{
            return 2;
        }
    }
    public static function checkDelimAnswers($userAnswers,$delimAnswers){
        $count=0;
        $array=array();
        foreach ($delimAnswers as $delimAnswer) {
            if (in_array($delimAnswer,$userAnswers)){
                $count++;
                array_push($array,$delimAnswer);
            }
        }
        return $array;
    }
    public static function generateMultipleBlock($usAnswers, $trueAnswers,$question,$blockUserAnswers){
        $count=0;
        $uAnsws=array();
        $string="";
        $isTrue=0;
        foreach ($trueAnswers as $trueAnswer) {
            if (in_array($trueAnswer,$usAnswers)){
                array_push($uAnsws,$trueAnswer);
                $count++;
            }
        }

        if ($count==count($trueAnswers)){
            $isTrue=1;
        }
        elseif ($count==0){
            $isTrue=0;
        }
        else{
            $isTrue=2;
        }

        if($isTrue==1) {
            $class = "alert alert-success";
            $phrase="Вы правильно ответили на вопрос";
        }
        elseif($isTrue==0){
            $class = "alert alert-danger";
            $phrase="Вы не правильно ответили на вопрос";
        }
        elseif($isTrue==2){
            $class = "alert alert-info";
            $phrase="Вы ответили на вопрос правильно частично";
        }
        $tAnsws=Answer::findAll(['id'=>$trueAnswers]);
        $usAnsws=Answer::findAll(['id'=>$blockUserAnswers]);
        $string.="<div class='".$class."'>";
        $string.="<p>Правильные ответы: <ul>";
        foreach ($tAnsws as $tAnsw){
            $string.="<li>".$tAnsw->title."</li>";
        }
        $string.="</ul></p>";
        $string.="<p>Вы ответили: <ul>";
        foreach ($usAnsws as $usAnsw) {
            $string.="<li>".$usAnsw->title."</li>";
        }
        $string.="</ul></p>";
        $string.="<p>".$phrase."</p>";
        
        $string.="</div>";
        return $string;
    }
}
