<?php

//  constants
define('QPF_DIR'        , dirname(__DIR__));
define('DS'             ,DIRECTORY_SEPARATOR);
define('QPF_DIR_APP'    , QPF_DIR.DS."app");
define('QPF_DIR_CFG'    , QPF_DIR_APP.DS."Cfg");
define('QPF_DIR_INC'    , QPF_DIR.DS."inc");
define('QPF_DIR_PUB'    , QPF_DIR.DS."pub");
define('QPF_DIR_TMP'    , QPF_DIR.DS."tmp");
    define('QPF_DIR_LOG'    , QPF_DIR_TMP.DS."log");


// autoloader
require QPF_DIR_INC."/Qpf/Core/Autoloader.php";
use Qpf\Core\Autoloader;
(new Autoloader())->addNamespace("App",QPF_DIR_APP);

use Qpf\Qpf;

// defaults settings for the application
if (file_exists(QPF_DIR_CFG.DS.'factory.php')) {
    require_once QPF_DIR_CFG.DS.'factory.php';
} else {
    Qpf::log("Missing cfg/factory.php file",array('color' => "red"));
}

// local config like database etc ...
if (file_exists(QPF_DIR_CFG.DS.'local.php')) {
    require_once QPF_DIR_CFG.DS.'local.php';
} else {
    Qpf::log("Missing cfg/local.php file",array('color' => "red"));
}