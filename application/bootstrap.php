<?php

namespace Whatswrong;

// Require base classes

require_once 'core/controller.php';
require_once 'core/route.php';
require_once 'core/config.php';
require_once 'core/data-mapper.php';
require_once 'core/app.php';

App::init(); // configurations, database and file system

Route::start(); // start router