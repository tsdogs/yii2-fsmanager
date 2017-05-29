<?php

namespace tsdogs\fsmanager;

use Yii;
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
        $this->registerTranslations();
        parent::init();

        if (empty($this->publicPath) || empty($this->tempPath)) {
            throw new Exception('Setup {publicPath} and {tempPath} in module properties');
        }

        $this->rules = ArrayHelper::merge(['maxFiles' => 3], $this->rules);
        $this->defaultRoute = 'fs';
    }

    public function registerTranslations()
    {
    
        if (!isset(Yii::$app->get('i18n')->translations['fsmanager*'])) {
            Yii::$app->get('i18n')->translations['fsmanager*'] = [
                'class'    => 'yii\i18n\PhpMessageSource',
                'basePath' => __DIR__ . '/messages',
                'sourceLanguage' => 'en',
            ];
        }    
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