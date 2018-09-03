<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use mongosoft\file\UploadBehavior;

/**
 * This is the model class for table "{{%file}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $file
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property ThemeFile[] $themeFiles
 *
 * @property string $url
 */
class File extends \yii\db\ActiveRecord
{
    const SCENARIO_INSERT = 'insert';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }
    
    public function behaviors()
    {
        return [
            'ts' => TimestampBehavior::className(),
            'upload' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'file',
                'scenarios' => [self::SCENARIO_INSERT],
                'path' => '@webroot/uploads',
                'url' => '@web/uploads',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required', 'on' => self::SCENARIO_INSERT],
            [['name'], 'string', 'max' => 255],
            ['file', 'file', 'extensions' => self::extensions(), 'skipOnEmpty'=> false, 'on' => self::SCENARIO_INSERT],
        ];
    }

    public static function extensions()
    {
        return [
            //'doc',
            //'docx',
            //'pdf',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file' => 'File',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getThemeFiles()
    {
        return $this->hasMany(ThemeFile::className(), ['file_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return FileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FileQuery(get_called_class());
    }


    /**
     * @return null|string
     */
    public function getUrl()
    {
        /** @var $this File|UploadBehavior */
        return $this->getUploadUrl('file');
    }
}
