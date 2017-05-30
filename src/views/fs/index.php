<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\popover\PopoverX;
use kartik\dialog\Dialog;

Yii::$app->formatter->sizeFormatBase = 1000;
$this->title = Yii::t('fsmanager','Documents').' '.$path;

$fp = '';
$links = [];
foreach (explode(DIRECTORY_SEPARATOR, $path) as $p) {
    if ($p!='') {
        $links[] = [
            'label' => $p,
            'url' => ['index','p'=>$fp.DIRECTORY_SEPARATOR.$p],
        ];
        $fp .= $fp.DIRECTORY_SEPARATOR.$p;
    }
}
$toolbar = false;
if ($upload) {
    $this->beginBlock('toolbar');
    PopoverX::begin([
            'header' => Yii::t('fsmanager','Create directory'),
            'headerOptions' => ['encode'=>false,],
            'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
            'footer' => Html::button(Yii::t('fsmanager','Create'), [
                'class'=>'btn btn-sm btn-primary',
                'onclick' => '$("#create-dir").trigger("submit")'
                ]),
            'toggleButton' => [
                'label'=>'<i class="glyphicon glyphicon-folder-close"></i> '.Yii::t('fsmanager','Create directory'), 
                'class'=>'btn btn-warning',
            ],
        ]);
    echo Html::beginForm(['create','p'=>$path],'POST',['id'=>'create-dir']);
   
    echo Html::textInput('directory','',[]);
    echo Html::endForm();
    PopoverX::end();

    echo Html::a('<i class="glyphicon glyphicon-upload"></i> '.Yii::t('fsmanager','Upload'),['index'],[
                'class'=>'btn btn-success',
                'onclick'=>'$(\'#attach\').modal(); return false;',
        ]); 
                    
       
    echo Html::a('<i class="glyphicon glyphicon-remove"></i> '.Yii::t('fsmanager','Delete'),'#',[
        'class'=>'btn btn-danger',
        'onclick' => 'if (confirm("'.Yii::t('fsmanager','Are you sure you want to remove selected elements?').'")) { $("#delete-form").trigger("submit"); } return false;',
    ]); 

    $this->endBlock();
    
    $toolbar = $this->blocks['toolbar'];
    
    Modal::begin([
        'header' => '<h2>'.Yii::t('fsmanager','Upload').'</h2>',
        'id' => 'attach',
        'size' => 'modal-lg',
    ]);
    echo $this->render('upload',['path'=>$path]);
    Modal::end();

    echo Dialog::widget([
        'dialogDefaults'=>[
            Dialog::DIALOG_PROMPT => [
                'draggable' => false,
                'title' => Yii::t('fsmanager','Rename'),
                'buttons' => [
                    [
                        'label' => Yii::t('fsmanager','Cancel'),
                        'icon' => Dialog::ICON_CANCEL
                    ],
                    [
                        'label' => Yii::t('fsmanager','OK'),
                        'icon' => Dialog::ICON_OK,
                        'class' => 'btn-primary'
                    ],
                ]
            ],
        ]
    ]);
    
}
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'fsgrid',
    'columns' => [
        [
            'class'=>'kartik\grid\CheckboxColumn',
            'checkboxOptions' => function ($model) {
                return ['value'=>$model['name']];
            },
            'visible' => $upload,
        ],
        [
            'attribute' => 'name',
            'format' => 'html',
            'value' => function ($model) {
                if ($model['type']==1) {
                    return Html::a('<i class="glyphicon glyphicon-folder-close"></i> '.$model['name'],['index','p'=>$model['path'].DIRECTORY_SEPARATOR.$model['name']],[]);
                } else {
                    return Html::a('<i class="glyphicon glyphicon-file"></i> '.$model['name'],['view','p'=>$model['path'].DIRECTORY_SEPARATOR.$model['name']],[]);
                }
            },
            'label' => Yii::t('fsmanager','Name'),
        ],
        [
            'attribute' => 'name',
            'hidden' => true,
        ],
        [
            'attribute'=>'size',
            'value'=> function ($model) { return isset($model['size'])?Yii::$app->formatter->asShortSize($model['size'],1):''; },
            'format'=>'raw',
            'contentOptions'=>['style' => 'text-align: right'],
            'label' => Yii::t('fsmanager','Size'),
        ],
        [
            'attribute' => 'time',
            'format' => 'datetime',
            'contentOptions'=>['style' => 'text-align: right'],
            'label' => Yii::t('fsmanager','Date'),
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{rename}',
            'options'=>['style'=>'width: 100px; text-align:right;',],
            'buttons' => [
                'rename' => function ($url,$model,$key) {
                    $name = $model['name'];
                    $url = Url::to(['rename','p'=>$model['path'],'id'=>$model['name']]);
                    $label = '<label for="krajee-dialog-prompt" class="control-label"> '.Yii::t('fsmanager','New name').'</label>';
                    $input = '<input type="text" name="krajee-dialog-prompt" class="form-control" placeholder="Inserire il nuovo nome" value="'.$name.'" >';
                    $click = <<<__EOF
krajeeDialog.prompt('$label $input', function (result) {
    if (result) {
        $.ajax({
            data: { name: result },
            url: '$url',
        }).done(function(data) {
            window.location.reload();
        });
    }
}); 
    return false;
__EOF;
                    return Html::a('<i class="glyphicon glyphicon-edit"></i>','#',[
                        'class' => 'btn-sm btn-default',
                        'title' => Yii::t('fsmanager','Rename'),
                        'onclick' => $click,
                    ]);
                },
            ],
            'header' => Yii::t('fsmanager','Actions'),
        ],
        
    ],
    'panel' => [
        'type'=>'info',
        'heading' => $this->title,
        'before' => yii\widgets\Breadcrumbs::widget([
            'homeLink' => [
                'label' => '<i class="glyphicon glyphicon-folder-open"></i> Home',
                'url'=>['index', 'p'=>'/'],
                ],
            'links' => $links,
            'encodeLabels'=>false,
        ]).Html::beginForm(['delete','p'=>$path],'POST',['id'=>'delete-form']),
        'after' => Html::endForm(),
    ],
    'toolbar' => $toolbar,
]);

