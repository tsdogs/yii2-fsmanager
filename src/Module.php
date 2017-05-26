<?php

namespace tsdogs\fsmanager;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\i18n\PhpMessageSource;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'tsdogs\fsmanager\controllers';

    public $publicPath = '@app/uploads/store';

    public $tempPath = '@app/uploads/temp';
    
    public $accessRoles = ['*'];
    
    public $uploadRoles = ['*'];

    public $rules = [];


    public function init()
    {
        parent::init();

        if (empty($this->publicPath) || empty($this->tempPath)) {
            throw new Exception('Setup {publicPath} and {tempPath} in module properties');
        }

        $this->rules = ArrayHelper::merge(['maxFiles' => 3], $this->rules);
        $this->defaultRoute = 'fs';
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        \Yii::$app->i18n->translations['tsdogs/*'] = [
            'class' => PhpMessageSource::className(),
            'sourceLanguage' => 'en',
            'publicPath' => '@vendor/tsdogs/yii2-fsmanager/src/messages',
            'fileMap' => [
                'tsdogs/fsmanager' => 'fsmanager.php'
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return \Yii::t('tsdogs/' . $category, $message, $params, $language);
    }

    public function getPublicPath()
    {
        return \Yii::getAlias($this->publicPath);
    }

    public function getTempPath()
    {
        return \Yii::getAlias($this->tempPath);
    }

    public function getUserDirPath()
    {
        \Yii::$app->session->open();

        $userDirPath = $this->getTempPath() . DIRECTORY_SEPARATOR . \Yii::$app->session->id;
        FileHelper::createDirectory($userDirPath);

        \Yii::$app->session->close();

        return $userDirPath . DIRECTORY_SEPARATOR;
    }

    public function getShortClass($obj)
    {
        $className = get_class($obj);
        if (preg_match('@\\\\([\w]+)$@', $className, $matches)) {
            $className = $matches[1];
        }
        return $className;
    }

}