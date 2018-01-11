<?php

class Router
{

    private $routes; //список маршрутов

    public function __construct()
    {
        // подключение маршрутов из файла routes.php
        $routesPath = ROOT.'/config/routes.php';
        $this->routes = include($routesPath);
    }

    private function getURI()
    {
        //метод получает url, который прописывается в браузерной строке
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/'); //всё после /
        }
    }

    //запуск стартовой страницы сайта
    private function openStartingPage() {
        $actionName = 'actionMain'; //создание экшэна
        $controllerName = 'MainController'; //создание контроллера

        $controllerFile = ROOT . '/controllers/' .$controllerName. '.php'; //подключение файла контроллера
        if (file_exists($controllerFile)) {
            include_once($controllerFile);
        }
        //создание экземпляра класса контроллера, и вызов у данного класса метода $actionName
        $parameters = array();
        $controllerObject = new $controllerName;
        call_user_func_array(array($controllerObject, $actionName), $parameters);
    }

    public function run() //запуск маршрутизации
    {
        $uri = $this->getURI();
        if ($uri == '' or $uri == '/'){ //получили uri, если это имя сайта, или после слэша, то запускаем стартовую страницу
            $this->openStartingPage();
            return;
        }

        $routeExists = false;

        //пробегаем по списку всех маршрутов, и ищем тот что совпадает с uri
        foreach ($this->routes as $uriPattern => $path) {
        //если совпадение найдено
            if(preg_match("~$uriPattern~", $uri)) {

                // Получаем внутренний путь из внешнего согласно правилу.
                // Ex: "analysis" => "Analysis/view"
                $internalRoute = preg_replace("~$uriPattern~", $path, $uri);

                // проверяем uri на наличие get запроса
                $get_check = explode("?", $internalRoute);


                $if_get_found = FALSE;

                //проверяем, есть ли get запрос
                if (stristr($internalRoute, '?') !== FALSE) {
                    if (isset($_GET) && count($get_check) == 2 && strlen($get_check[1]) > 0){
                        $internalRoute = $get_check[0];
                        $if_get_found = TRUE;
                    } else {
                        $routeExists = FALSE;
                        break;
                    }
                }

                //разбиваем строку по / на элементы
                $segments = explode('/', $internalRoute);

                //формирование имени контроллера
                $controllerName = array_shift($segments).'Controller';
                $controllerName = ucfirst($controllerName);

                //формирование иимени action
                $actionName = 'action'.ucfirst(array_shift($segments));
                $parameters = $segments;

                $controllerFile = ROOT . '/controllers/' .$controllerName. '.php'; //подключение файла контроллера
                if (file_exists($controllerFile)) {
                    include_once($controllerFile);
                }

                $controllerObject = new $controllerName;
                $result = call_user_func_array(array($controllerObject, $actionName), $parameters);

                if ($result != null)
                {
                    //возвращает null, если не находит такой метод в классе
                    $routeExists = true; //фиксируем нахождение нужного маршрута
                    break; //останавливаем рассмотрение остальных маршрутов
                }
            }
        }

        if (!$routeExists)
        {
            //если маршрут не найден, выводим ошибку
            require_once(ROOT . '/views/template/404.php');
        }
    }
}