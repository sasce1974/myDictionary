<?php
$router = new Core\Router();


// Add the routes
$router->add('contact', ['controller' => 'Contact', 'action' => 'index']);
$router->add('login', ['namespace'=>'Auth', 'controller' => 'Login', 'action' => 'index']);
$router->add('login/new', ['namespace'=>'Auth', 'controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['namespace'=>'Auth', 'controller' => 'Login', 'action' => 'logout']);

$router->add('register', ['namespace'=>'Auth', 'controller' => 'Register', 'action' => 'index']);
$router->add('register/store', ['namespace'=>'Auth', 'controller' => 'Register', 'action' => 'store']);
$router->add('register/checkEmailExist', ['namespace'=>'Auth', 'controller' => 'Register', 'action' => 'checkEmailExist']);

$router->add('kanjies', ['controller' => 'Kanjies', 'action' => 'index']);

$router->add('', ['controller' => 'Words', 'action' => 'index']);
$router->add('{controller}', ['action' => 'getAll']);
$router->add('{controller}/{id:\d+}');
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('{controller}/{id:\d+}/{action}/{aid:\d+}');







$router->dispatch($_SERVER['QUERY_STRING']);