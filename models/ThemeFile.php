<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%theme_file}}".
 *
 * @property integer $id
 * @property integer $theme_id
 * @property integer $file_id
 * @property string $name
 * @property integer $type
 * @property integer $created_at
 *
 * @property File $file
 * @property Theme $theme
 */
class ThemeFile extends \yii\db\ActiveRecord
{
    const TYPE_ATTACHED = 10;
    const TYPE_BOOK = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%theme_file}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'ts' => [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['theme_id', 'file_id', 'type'], 'required'],
            [['theme_id', 'file_id', 'type', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']],
            [['theme_id'], 'exist', 'skipOnError' => true, 'targetClass' => Theme::className(), 'targetAttribute' => ['theme_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'theme_id' => 'Theme ID',
            'file_id' => 'File ID',
            'name' => 'Название файла',
            'type' => 'Type',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(File::className(), ['id' => 'file_id']);
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
     * @return ThemeFileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ThemeFileQuery(get_called_class());
    }

    public static function typeList()
    {
        return [
            self::TYPE_ATTACHED => 'Дополнительные файлы',
            self::TYPE_BOOK => 'Рекомендуемая литература',
        ];
    }
}
