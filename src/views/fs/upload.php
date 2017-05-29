<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\FileInput;


?>
<div id="upload-form">
<?= $path ?>
<form action="<?= Url::to(['upload','p'=>$path]) ?>" enctype="multipart/form-data" >

<?= FileInput::widget([
    'name' => 'upload[]',
    'language' => 'it',
    'options' => ['multiple' => true],
    'pluginOptions' => [
        'showPreview'=>true,
        'preferIconicPreview'=>true,
        'dropZoneEnabled'=>true,
        //'previewFileType' => false, 
        //'allowedPreviewMimeTypes' => [],
        //'allowedPreviewTypes'=>false,
        'previewIcon'=> '<i class="glyphicon glyphicon-file"></i> aaa',
        'uploadUrl' => Url::to(['upload','p'=>$path]),
        'fileActionSettings' => [
            'showZoom' => false,
            'showUpload'=> false,
            'showRemove' => false,
            'showDrag'=>false,
        ],
        'browseOnZoneClick' => true,
        'previewSettings' => [
            'image'=> ['width'=> "auto", 'height'=> "100px"],
            'html'=> ['width'=> "133px", 'height'=> "100px"],
            'text'=> ['width'=> "100px", 'height'=> "10px"],
            'video'=> ['width'=> "133px", 'height'=> "100px"],
            'audio'=> ['width'=> "133px", 'height'=> "80px"],
            'flash'=> ['width'=> "133px", 'height'=> "100px"],
            'object'=> ['width'=> "133px", 'height'=> "100px"],
            'other'=> ['width'=> "100px", 'height'=> "100px"]
        ],
    ],
    'pluginEvents'=>[
        'filebatchuploadcomplete'=>'function() { location.reload();  } ',
    ]
]); ?>

<hr />
<div>
    <div class="form-group pull-right">
        <?= Html::a('<i class="glyphicon glyphicon-cancel"></i> '.Yii::t('fsmanager','Cancel'), '#', ['class' => 'btn btn-default','
                    data-dismiss'=>"modal"]) ?>
    </div>
<div class="clearfix"></div>
</div>
</form>
</div>
