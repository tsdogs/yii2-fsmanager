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

    /**
     * @param $filePath string
     * @param $owner
     * @return bool|File
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    /*public function attachFile($filePath, $owner)
    {
        if (empty($owner->id)) {
            throw new Exception('Parent model must have ID when you attaching a file');
        }
        if (!file_exists($filePath)) {
            throw new Exception("File $filePath not exists");
        }

        $fileHash = md5(microtime(true) . $filePath);
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
        $newFileName = "$fileHash.$fileType";
        $fileDirPath = $this->getFilesDirPath($fileHash);
        $newFilePath = $fileDirPath . DIRECTORY_SEPARATOR . $newFileName;

        if (!copy($filePath, $newFilePath)) {
            throw new Exception("Cannot copy file! $filePath  to $newFilePath");
        }

        $file = new File();
        $file->name = pathinfo($filePath, PATHINFO_FILENAME);
        $file->model = $this->getShortClass($owner);
        $file->itemId = $owner->id;
        $file->hash = $fileHash;
        $file->size = filesize($filePath);
        $file->type = $fileType;
        $file->mime = FileHelper::getMimeType($filePath);

        if ($file->save()) {
            unlink($filePath);
            return $file;
        } else {
            return false;
        }
    }

    public function detachFile($id)
    {
        $file = File::findOne(['id' => $id]);
        if (empty($file)) return false;
        $filePath = $this->getFilesDirPath($file->hash) . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;
        
        // this is the important part of the override.
        // the original methods doesn't check for file_exists to be 
        return file_exists($filePath) ? unlink($filePath) && $file->delete() : $file->delete();
    }*/
}