<?php
use yii\web\JsExpression;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use app\modules\api\assets\TinymceUploadHendlersAsset;
use dosamigos\tinymce\TinyMce;
/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\settings\Main */
/* @var $form yii\widgets\ActiveForm */
TinymceUploadHendlersAsset::register($this);
$this->title = 'Основные настройки сайта';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>
<?php $form = ActiveForm::begin(['id' => 'main-settings-form']); ?>
<?= $form->field($model, 'siteName') ?>
<?= $form->field($model, 'homeContent')->widget(TinyMce::className(), [
    'language' => Yii::$app->language,
    'clientOptions' => [
        'plugins' => [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table imagetools contextmenu paste autoresize"
        ],
        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        'file_picker_callback' => new JsExpression('window.tinymce_file_picker_callback'),
        'images_upload_handler' => new JsExpression('window.tinymce_images_upload_handler'),
    ]
]) ?>

<?= $form->field($model, 'contactsContent')->widget(TinyMce::className(), [
    'language' => Yii::$app->language,
    'clientOptions' => [
        'plugins' => [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table imagetools contextmenu paste autoresize"
        ],
        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        'file_picker_callback' => new JsExpression('window.tinymce_file_picker_callback'),
        'images_upload_handler' => new JsExpression('window.tinymce_images_upload_handler'),
    ]
]) ?>
<?= $form->field($model, 'adminEmail') ?>
<div class="form-group">
    <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
