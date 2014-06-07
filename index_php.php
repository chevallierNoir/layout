<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Suarez
 * Date: 07/06/14
 * Time: 11:08
 */

define('__ROOT__',dirname(dirname(__FILE__)));
define('__APPLICATION__',dirname(__FILE__));
require_once('.core/Application.php');
var_dump(__ROOT__);
Application::loader(dirname(__FILE__));
Application::createModelsJS(dirname(__FILE__));
/** @var Analyst $analyst */
$analyst = Analyst::readOperation(['id'=> 2]);

var_dump($analyst[0]->get('Project'));