<?php
/**
 * Created by PhpStorm.
 * User: elnacabotparedes
 * Date: 13/4/17
 * Time: 19:12
 */



$app->get('/hello', 'SilexApp\\Controller\\HelloController::indexAction');
$app->get('/add/{num1}/{num2}', 'SilexApp\\\Controller\\HelloController::addAction');