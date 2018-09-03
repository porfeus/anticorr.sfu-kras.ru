<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_done}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $module_id
 * @property integer $theme_id
 * @property integer $count_try
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $tests_failed
 *
 * @property Module $module
 * @property Theme $theme
 * @property User $user
 */
class UserDone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_done}}';
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
            [['user_id', 'module_id'], 'required'],
            [['user_id', 'module_id', 'theme_id', 'count_try', 'created_at', 'updated_at'], 'integer'],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::className(), 'targetAttribute' => ['module_id' => 'id']],
            [['theme_id'], 'exist', 'skipOnError' => true, 'targetClass' => Theme::className(), 'targetAttribute' => ['theme_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['tests_failed', 'boolean'],
            ['tests_failed', 'default', 'value' => null],
            [['user_id', 'module_id', 'theme_id'], 'unique', 'targetAttribute' => ['user_id', 'module_id', 'theme_id']],
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
            'count_try' => 'Count Try',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return UserDoneQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserDoneQuery(get_called_class());
    }
}
