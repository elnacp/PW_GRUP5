<?php
/**
 * Created by PhpStorm.
 * User: elnacabotparedes
 * Date: 5/4/17
 * Time: 18:31
 */

/*ini_set('display_errors',0);
require_once  __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../app/app.php';
require __DIR__.'/../app/config/prod.php';
$app->run();*/

ini_set('display_errors',0);
require_once  __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__.'/../app/app.php';
require __DIR__.'/../app/config/prod.php';
require __DIR__.'/../app/config/routes.php';
$app->run();