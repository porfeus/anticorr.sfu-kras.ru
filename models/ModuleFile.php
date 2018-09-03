<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%module_file}}".
 *
 * @property integer $id
 * @property integer $module_id
 * @property integer $file_id
 * @property string $name
 * @property integer $type
 * @property integer $created_at
 *
 * @property File $file
 * @property Module $module
 */
class ModuleFile extends \yii\db\ActiveRecord
{
    const TYPE_ATTACHED = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%module_file}}';
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
            [['module_id', 'file_id', 'type', 'created_at'], 'required'],
            [['module_id', 'file_id', 'type', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::className(), 'targetAttribute' => ['module_id' => 'id']],
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
    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }

    /**
     * @inheritdoc
     * @return ModuleFileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ModuleFileQuery(get_called_class());
    }

    public static function typeList()
    {
        return [
            self::TYPE_ATTACHED => 'Дополнительные файлы',
        ];
    }
}
