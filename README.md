Yii2 fsmanager
================
[![Latest Stable Version](https://poser.pugx.org/tsdogs/yii2-fsmanager/v/stable)](https://packagist.org/packages/tsdogs/yii2-fsmanager)
[![License](https://poser.pugx.org/tsdogs/yii2-fsmanager/license)](https://packagist.org/packages/tsdogs/yii2-fsmanager)
[![Build Status](https://scrutinizer-ci.com/g/tsdogs/yii2-fsmanager/badges/build.png?b=tests)](https://scrutinizer-ci.com/g/tsdogs/yii2-fsmanager/build-status/tests)
[![Code Coverage](https://scrutinizer-ci.com/g/tsdogs/yii2-fsmanager/badges/coverage.png?b=tests)](https://scrutinizer-ci.com/g/tsdogs/yii2-fsmanager/?branch=tests)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tsdogs/yii2-fsmanager/badges/quality-score.png?b=tests)](https://scrutinizer-ci.com/g/tsdogs/yii2-fsmanager/?branch=tests)
[![Total Downloads](https://poser.pugx.org/tsdogs/yii2-fsmanager/downloads)](https://packagist.org/packages/tsdogs/yii2-fsmanager)

Extension for file managing files on filesysstem

Demo
----
You can see the demo on the [krajee](http://plugins.krajee.com/file-input/demo) website

Installation
------------
<!--  -->
1. The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

	Either run
	
	```
	php composer.phar require tsdogs/yii2-fsmanager "~1.0.0"
	```
	
	or add
	
	```
	"tsdogs/yii2-fsmanager": "~1.0.0"
	```
	
	to the require section of your `composer.json` file.

2.  Add module to `common/config/main.php`
	
	```php
	'modules' => [
		...
		'fsmanager' => [
			'class' => tsdogs\fsmanager\Module::className(),
			'tempPath' => '@app/uploads/temp',
			'basePath' => '@app/uploads/store',
			'rules' => [ // Rules according to the FileValidator
			    'maxFiles' => 10, // Allow to upload maximum 3 files, default to 3
				'mimeTypes' => 'image/png', // Only png images
				'maxSize' => 1024 * 1024 // 1 MB
			],
		]
		...
	]
	```
	

2. Make sure that you specified `maxFiles` in module rules and `maxFileCount` on `AttachmentsInput` to the number that you want



Change log
----------

- **May 26, 2017**  - 	Initial version
