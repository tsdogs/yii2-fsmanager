<?php

namespace tsdogs\fsmanager\controllers;

use Yii;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;



class FsController extends Controller 
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    ///foreach ($this->getModules()as $m) { echo $m::className().'<br />'; } exit;
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                //'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [                                                                                                                             
                        'actions' => [ 'index','view', ],
                        'allow' => true,                                                                                                          
                        'roles' => $this->module->accessRoles,                                                                                                    
                    ],
                    [                                                                                                                             
                        'actions' => [ 'create', 'upload', 'delete', 'rename' ],
                        'allow' => true,                                                                                                          
                        'roles' => $this->module->uploadRoles,                                                                                                    
                    ],
                ],
            ],
        ];
    }
    
    private function getModule()
    {
        return end($this->getModules());
    }

    public function actionIndex($p = null)
    {
        if ($p == null) {
            $path = '';
            $dir = $this->module->getPublicPath() . DIRECTORY_SEPARATOR;
        } else {
            // For security do not allow paths with ..
            $path = str_replace('..','',FileHelper::normalizePath($p));
            // TODO: verificare se la cartella esiste
            $dir = $this->module->getPublicPath() . DIRECTORY_SEPARATOR . $path;
        }
        $dir = FileHelper::normalizePath($dir);
        $content = [
            'directories' => [],
            'files' => [],
        ];
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR .$entry)) {
                        $content['directories'][] = $entry;
                    } else {
                        $content['files'][] = $entry;
                    }
                }
            }
            closedir($handle);
        }        
        natcasesort($content['directories']);
        natcasesort($content['files']);
        $elements = [];
        foreach ($content['directories'] as $f) {
            $elements[]=[
                'type' => 1,
                'path' => $path,
                'time' => @filemtime($dir.DIRECTORY_SEPARATOR.$f),
                'name' => $f,
            ];
        }
        foreach ($content['files'] as $f) {
            $elements[]=[
                'type' => 2,
                'path' => $path,
                'name' => $f,
                'time' => @filemtime($dir.DIRECTORY_SEPARATOR.$f),
                'mime' => @FileHelper::getMimeType($dir.DIRECTORY_SEPARATOR.$f),
                'size' => @filesize($dir.DIRECTORY_SEPARATOR.$f),
            ];
        }
        $dataProvider = new ArrayDataProvider([
            'models' => $elements,
            'pagination' => false,
        ]);
        return $this->render('index',['dataProvider'=>$dataProvider,'path'=>$path,'upload'=>$this->canUpload()]);
    }
    
    public function actionView($p) {
        $path = FileHelper::normalizePath($p);
        $file = $this->module->getPublicPath() . DIRECTORY_SEPARATOR . $path;
        if (strlen($file)>strlen($this->module->getPublicPath())) {
            if (is_file($file)) {
                return Yii::$app->response->sendFile($file, basename($file));
            } else {
                throw new yii\web\NotFoundHttpException('The requested file does not exist.');
            }
        } else {
            throw new yii\web\ForbiddenHttpException('You have no access rights for this path.');
        }
    }
    
    public function actionUpload($p) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $path = FileHelper::normalizePath(urldecode($p));
        $dir = $this->module->getPublicPath() . DIRECTORY_SEPARATOR . $path;
        $dir = FileHelper::normalizePath($dir);
        if (is_dir($dir)) {
            $files = \yii\web\UploadedFile::getInstancesByName('upload');
            $ok = true;
            if (count($files)>0) {
                foreach($files as $file) {
                    if ($file->saveAs($dir.DIRECTORY_SEPARATOR.$file->name)) {
                        $ok = $ok && true;
                    } else {
                        $ok = false;
                    }
                }
                
                if ($ok) {
                    return [ ];
                } else {
                    return [ 'error'=>Yii::t('fsmanager','File upload failed!') ];
                }
            } else {
                return [ 'error'=>Yii::t('fsmanager','Files were not accepted. (Size is too big?)') ];
            }
        }
        return [ 'error'=>Yii::t('fsmanager','Something went wrong!') ];
    }
    
    public function actionCreate($p)
    {
        if (isset(Yii::$app->request->post()['directory'])) {
            $d = str_replace(DIRECTORY_SEPARATOR,'_',Yii::$app->request->post()['directory']);
            if ($d != '') {
                $path = FileHelper::normalizePath($p . DIRECTORY_SEPARATOR . $d);
                $dir = $this->module->getPublicPath() . DIRECTORY_SEPARATOR . $path;
                if (!FileHelper::createDirectory($dir)) {
                    Yii::$app->setFlash('error',Yii::t('fsmanager','Directory could not be created!'));
                }
            }
        }
        return $this->redirect(['index','p'=>$p]);
    }
    
    public function actionDelete($p)
    {
        $path = FileHelper::normalizePath($p);
        $base = $this->module->getPublicPath() . DIRECTORY_SEPARATOR . $path;
        if (isset(Yii::$app->request->post()['selection']) and is_array(Yii::$app->request->post()['selection'])) {
            foreach (Yii::$app->request->post()['selection'] as $f) {
                $x = FileHelper::normalizePath($base. DIRECTORY_SEPARATOR . $f);
                if (is_dir($x)) {
                    FileHelper::removeDirectory($x);
                } else {
                    unlink($x);
                }
            }
        }
        return $this->redirect(['index','p'=>$p]);
    }
    
    public function actionRename($p,$id,$name) 
    {
        if ($name!='') {
            $path = FileHelper::normalizePath($p);
            $base = $this->module->getPublicPath() . DIRECTORY_SEPARATOR . $path;
            $org = FileHelper::normalizePath($base . DIRECTORY_SEPARATOR . $id);
            $dest = FileHelper::normalizePath($base . DIRECTORY_SEPARATOR . $name);
            if (!rename($org,$dest)) {
                Yii::$app->user->setFlash(Yii::t('fsmanager','Could not rename element'));
            }
        }
        return $this->redirect(['index','p'=>$p]);
    }
    
    private function canUpload()
    {

        $roles = $this->module->uploadRoles;
        if (empty($roles)) {
            return true;
        }
        foreach ($roles as $role) {
            if ($role === '?') {
                if (Yii::$app->user->getIsGuest()) {
                    return true;
                }
            } elseif ($role === '@') {
                if (!Yii::$app->user->getIsGuest()) {
                    return true;
                }
            } elseif (Yii::$app->user->can($role)) {
                return true;
            }
        }
        return false;
    }
}
