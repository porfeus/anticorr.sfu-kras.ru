<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 28.07.2017
 * Time: 3:30
 */

namespace app\modules\api\models;


use yii\helpers\ArrayHelper;
//use app\modules\api\models\ThemeFile;

/**
 * Class Theme
 *
 * @property array $filesAttached
 * @property array $filesBook
 * @property ThemeFile[] $filesAttachedDb
 * @property ThemeFile[] $filesBookDb
 *
 * @package app\modules\api\models
 */
class Theme extends \app\models\Theme
{
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['filesAttached', 'filesValidate', 'params' => [
                'type' => ThemeFile::TYPE_ATTACHED,
                'storeModelsAttribute' => '_filesAttachedStore',
            ], 'skipOnEmpty' => false, 'isEmpty' => function($value) {
                return $value;
            }],
            ['filesBook', 'filesValidate', 'params' => [
                'type' => ThemeFile::TYPE_BOOK,
                'storeModelsAttribute' => '_filesBookStore',
            ], 'skipOnEmpty' => false],
            ['filesAttached', 'default', 'value' => []],
        ]);
    }
    
    public function fields()
    {
        return [
            'id',
            'parent_id',
            'themes',
            'module_id',
            'title',
            'description',
            'video_url',
            'sort',
            'filesAttached',
            'filesBook',
            'test_title',
            'test_comment_after',
            'button_name',
        ];
    }

    //public function extraFields()
    //{
    //    return ['filesAttached'];
    //}

    //public function getQuestions()
    //{
    //    return $this->hasMany(Question::className(), ['theme_id' => 'id', 'module_id' => 'module_id']);
    //}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Theme::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getThemes()
    {
        return $this->hasMany(Theme::className(), ['parent_id' => 'id'])
            ->orderBy(['sort' => Theme::DEFAULT_SORT_ORDER]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilesAttachedDb()
    {
        return $this->hasMany(ThemeFile::className(), ['theme_id' => 'id'])->andWhere(['type' => ThemeFile::TYPE_ATTACHED]);
    }

    /** @var array */
    protected $_filesAttached;
    /** @var ThemeFile[] */
    protected $_filesAttachedStore = [];

    /**
     * @return array
     */
    public function getFilesAttached()
    {
        if ($this->_filesAttached === null) {
            $this->_filesAttached = ArrayHelper::toArray($this->filesAttachedDb);
        }
        return $this->_filesAttached;
    }

    /**
     * @param array $value
     */
    public function setFilesAttached($value)
    {
        $this->_filesAttached = $value;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilesBookDb()
    {
        return $this->hasMany(ThemeFile::className(), ['theme_id' => 'id'])->andWhere(['type' => ThemeFile::TYPE_BOOK]);
    }

    /** @var array */
    protected $_filesBook;
    /** @var ThemeFile[] */
    protected $_filesBookStore = [];

    /**
     * @return array
     */
    public function getFilesBook()
    {
        if ($this->_filesBook === null) {
            $this->_filesBook = ArrayHelper::toArray($this->filesBookDb);
        }
        return $this->_filesBook;
    }

    /**
     * @param array $value
     */
    public function setFilesBook($value)
    {
        $this->_filesBook = $value;
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function filesValidate($attribute, $params)
    {
        foreach ($this->$attribute as $index => $item) {
            $this->internalFileValidate(
                $attribute,
                $index,
                $item,
                $params['type'],
                $params['storeModelsAttribute']
            );
        }
    }

    protected function internalFileValidate($attribute, $index, $item, $type, $storeModelsAttribute)
    {
        $model = null; /** @var ThemeFile|null $model */
        if (!isset($item['id']) || empty($item['id']) || !($model = ThemeFile::findOne($item['id']))) {
            $model = new ThemeFile();
        }
        $model->load($item, '');
        $model->type = $type;
        if (!$model->validate()) {
            foreach ($model->getFirstErrors() as $attr => $error) {
                $this->addError("$attribute-$model->unique-$attr", $error);
            }
        }
        $this->{$storeModelsAttribute}[] = $model;
        //$this->addError('qqq', 'qqq');
    }

    public function updateFiles()
    {
        foreach (['Attached', 'Book'] as $attr) {
            $db = "files{$attr}Db";
            $store = "_files{$attr}Store";
            $local = "files{$attr}";
            $delete = ArrayHelper::map($this->$db, 'id', 'id');
            foreach ($this->$store as $model) { /** @var ThemeFile $model */
                $model->theme_id = $this->id;
                $model->save(false);
                if (isset($delete[$model->id])) {
                    unset($delete[$model->id]);
                }
            }
            $this->$local = $this->$store;
            ThemeFile::deleteAll(['in', 'id', $delete]);
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->updateFiles();
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }
}