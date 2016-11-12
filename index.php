<?php
/**
 * Zoo Simulator App
 */

/** @var Base $f3 */
$f3 = require('lib/base.php');
// set autoload path
$f3->set('AUTOLOAD','app/');

// force displaying errors
ini_set('display_errors', 0);
error_reporting(-1);

$f3->set('DEBUG', 2);

// define some routes
$f3->route('GET /', '\Controller\ZooController->listAnimals');
$f3->route('GET /list', '\Controller\ZooController->listAnimals');
$f3->route('GET /elapse-time', '\Controller\ZooController->elapseTime');
$f3->route('GET /feed-animals', '\Controller\ZooController->feedAnimals');
$f3->route('GET /reset', '\Controller\ZooController->resetSim');

$f3->run();