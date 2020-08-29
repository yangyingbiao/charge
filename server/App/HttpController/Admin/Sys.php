<?php
namespace App\HttpController\Admin;
class Sys extends Base {
    public function config() {
        $config = getConfig('SYS');
        return $this->successResult($config);
    }
}