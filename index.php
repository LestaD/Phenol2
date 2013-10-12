<?php
require 'phenol2/engine.php';

$registry->detector->searchPackagesIn(dirname(__FILE__).DS.'%package%'.DS);
$registry->detector->setDefaultPackage('default');

$registry->detector->detectDomainPackage('onlife.com');
$registry->detector->runPackage();

