<?php

    // FrontController
    //отображение ошибок
    ini_set('display_errors',1);
    error_reporting(E_ALL);

    // Подключение файлов системы
    define('ROOT', dirname(__FILE__));
    require_once(ROOT.'/components/Router.php');

    // Вызов Router
    $router = new Router();
    $router->run();