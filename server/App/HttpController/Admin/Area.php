<?php
namespace App\HttpController\Admin;
class Area extends Base {
    public function areaList() {
        $areaId = intval($this->GET['id'] ?? 0);
        $areaList = model('Area')->findAll(['parent_id' => $areaId]);
        return $this->successResult($areaList);
    }
}