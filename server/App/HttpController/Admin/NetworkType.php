<?php
namespace App\HttpController\Admin;
class NetworkType extends Base {
    private static function updateDeviceTypeCache() {
        getTask()->async(function() {
            $rows = model('NetworkType')->findAll(null, ['id', 'name']);
            if($rows) {
                $cache = [];
                foreach ($rows as $row) {
                    $cache[$row['id']] = $row['name'];
                }
                
                getRedis()->set('network.type', json_encode($cache));
            }
        });
    }
    
    public function getTypeList() { //
        $typeList = model('NetworkType')->findAll();
        return $this->successResult($typeList);
    }
    
    public function typeList() { //列表
        $typeList = model('NetworkType')->findAll();
        return $this->successResult($typeList);
    }
    
    public function addType() {
        try {
            $params = $this->POST;
            $parentId = intval($params['id'] ?? 0);
            $name = trim($params['name'] ?? '');
            if($name === '') return $this->errorResult();
            $data = [
                'name' => $name
            ];
            
            $model = model('NetworkType');
            
            if($model->isExistsByWhere(['name' => $data['name']])) {
                return $this->errorResult('已存在相同的类型');
            }
            
            $id = $model->add($data);
            if(empty($id)) {
                return $this->errorResult();
            }else {
                $data['id'] = $id;
                self::updateDeviceTypeCache();
                return $this->successResult($data);
            }
        } catch (\Exception $e) {
            return $this->errorResult();
        }
    }
    
    public function editType() {
        try {
            $params = $this->POST;
            $name = trim($params['name'] ?? '');
            $id = intval($params['id'] ?? 0);
            if($name === '' || $id <= 0) return $this->errorResult();
            
            $model = model('NetworkType');
            
            $row = $model->findOne(['id' => $id]);
            if(empty($row)) return $this->errorResult();
            if($row['name'] == $name) return $this->successResult();
            
            if($model->isExistsByWhere(['name' => $name])) {
                return $this->errorResult('已存在相同的分类');
            }
            
            if($model->modify(['id' => $id], ['name' => $name])) {
                self::updateDeviceTypeCache();
                return $this->successResult();
            }else {
                return $this->errorResult('修改失败');
            }
            
        } catch (\Exception $e) {
            return $this->errorResult();
        }
    }
    
    public function deleteType() {
        try {
            $id = intval($this->POST['id'] ?? 0);
            if(model('NetworkType')->delete(['id' => $id])) {
                self::updateDeviceTypeCache();
                getTask()->async(function() use($id) {
                    model('Device')->modify(['network_type' => $id], ['network_type' => 0]);
                });
                    return $this->successResult();
            }else {
                return $this->errorResult();
            }
            
        } catch (\Exception $e) {
            return $this->errorResult();
        }
    }
}