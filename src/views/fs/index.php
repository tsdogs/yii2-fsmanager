<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;

Yii::$app->formatter->sizeFormatBase = 1000;
$this->title = Yii::t('yii','Documents').' '.$path;

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
    $toolbar = Html::a('<i class="glyphicon glyphicon-folder-close"></i> '.Yii::t('yii','Crate directory'),['index'],['class'=>'btn btn-warning']).
                Html::a('<i class="glyphicon glyphicon-upload"></i> '.Yii::t('yii','Upload'),['index'],[
                    'class'=>'btn btn-success',
                    'onclick'=>'$(\'#attach\').modal(); return false;',
                ]).
                Html::a('<i class="glyphicon glyphicon-remove"></i> '.Yii::t('yii','Delete'),['index'],['class'=>'btn btn-danger']);
                
    Modal::begin([
        'header' => '<h2>'.Yii::t('yii','Upload').'</h2>',
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
            'label' => Yii::t('yii','Name'),
        ],
        [
            'attribute'=>'size',
            'value'=> function ($model) { return isset($model['size'])?Yii::$app->formatter->asShortSize($model['size'],1):''; },
            'format'=>'raw',
            'contentOptions'=>['style' => 'text-align: right'],
            'label' => Yii::t('yii','Size'),
        ],
        [
            'attribute' => 'time',
            'format' => 'datetime',
            'contentOptions'=>['style' => 'text-align: right'],
            'label' => Yii::t('yii','Date'),
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

