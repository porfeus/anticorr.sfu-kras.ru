<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "faq".
 *
 * @property integer $id
 * @property string $question
 * @property string $answer
 * @property integer $user_id
 * @property integer $create_date
 * @property integer $answer_date
 * @property integer $theme_id
 * @property integer $subject
 */
class Faq extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';

	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		return [
			self::SCENARIO_CREATE => ['user_id', 'subject', 'question', 'answer', 'theme_id', 'created_at'],
			self::SCENARIO_UPDATE => ['user_id', 'subject', 'question', 'answer', 'theme_id', 'answered_at', 'updated_at'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%faq}}';
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
			[['question', 'answer', 'subject'], 'string'],
			[['user_id', 'theme_id'], 'integer'],
			[['created_at', 'answered_at', 'updated_at'], 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'question' => 'Вопрос',
			'answer' => 'Ответ',
			'user_id' => 'Пользователь',
			'created_at' => 'Дата создания',
			'updated_at' => 'Дата редактирования',
			'answered_at' => 'Дата ответа',
			'theme_id' => 'Тема',
			'subject' => 'Тема сообщения',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAuthor()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
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
	 * @return FaqQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new FaqQuery(get_called_class());
	}
}
