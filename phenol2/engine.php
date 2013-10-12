<?php
define( 'ENGINE',			'Phenol');
define( 'VERSION',			'2.0.0' );

define( 'DS',				DIRECTORY_SEPARATOR );
define( 'DIR_ENGINE',		dirname(__FILE__) . DS );
define( 'DIR_ROOT',			dirname(DIR_ENGINE) . DS );
define( 'DIR_CORE',			DIR_ENGINE . 'core' . DS );
define( 'DIR_SYSTEM',		DIR_ENGINE . 'system' . DS );
define( 'DIR_DRIVER',		DIR_ENGINE . 'driver' . DS );

ini_set('register_globals', 'Off');

include DIR_SYSTEM . 'error.class.php';
include DIR_SYSTEM . 'engineblock.class.php';
include DIR_SYSTEM . 'package.class.php';
include DIR_SYSTEM . 'controller.class.php';
include DIR_SYSTEM . 'model.class.php';

include DIR_CORE . 'system.php';
include DIR_CORE . 'errorlistener.class.php';
include DIR_CORE . 'registry.class.php';
include DIR_CORE . 'detector.class.php';
include DIR_CORE . 'request.class.php';
include DIR_CORE . 'loader.class.php';
include DIR_CORE . 'config.class.php';
include DIR_CORE . 'db.class.php';
include DIR_CORE . 'local.class.php';
include DIR_CORE . 'template.class.php';


$registry = new Registry;
$registry->error = new Phenol2ErrorListener($registry);
$registry->request = new Request();
$registry->detector = new Detector($registry);
$registry->load = new Loader($registry);
$registry->config = new Config();
$registry->db = new Database();
$registry->locale = new Locale($registry);
$registry->view = new Template($registry);







