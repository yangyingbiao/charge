<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $this->setGlobalMode(true);
        $routeCollector->addGroup('/admin/', function (RouteCollector $collector) {
            foreach (ROUNTE as $key => $item) {
                foreach ($item as $k => $val) {
                    //$keys = explode(',', $key);
                    //$ks = explode(',', $k);
                    $collector->addRoute($val[0], $key . ($val[1] ? '/' . $val[1] : ''), '/admin/' . $key . '/' . $k);
                }
            }
        });
                    
    }
}