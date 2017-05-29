<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\popover\PopoverX;

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
    echo Html::a('<i class="glyphicon glyphicon-remove"></i> '.Yii::t('fsmanager','Delete'),['index'],['class'=>'btn btn-danger']); 

    $this->endBlock();
    
    $toolbar = $this->blocks['toolbar'];
    
    Modal::begin([
        'header' => '<h2>'.Yii::t('fsmanager','Upload').'</h2>',
        'id' => 'attach',
        'size' => 'modal-lg',
    ]);
    echo $this->render('upload',['path'=>$path]);
    Modal::end();
}
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
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
            'class'=>'kartik\grid\CheckboxColumn',
            'visible' => $upload,
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
        ])
    ],
    'toolbar' => $toolbar,
]);

