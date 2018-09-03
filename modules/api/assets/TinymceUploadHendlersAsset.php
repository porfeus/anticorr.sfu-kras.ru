<?php
namespace app\modules\api\assets;
use yii\web\AssetBundle;

/**
 * Created by PhpStorm.
 * User: User
 * Date: 02.08.2017
 * Time: 6:20
 */
class TinymceUploadHendlersAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/api/assets/tinymce';
    public $js = [
        'upload-handlers.js',
    ];
    public $publishOptions = [
        'only' => [
            'upload-handlers.js',
        ]
    ];
}