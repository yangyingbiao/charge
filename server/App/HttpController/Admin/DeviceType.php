<?php
namespace App\HttpController\Admin;
class DeviceType extends Base {
    private static function updateDeviceTypeCache() {
        getTask()->async(function() {
            $rows = model('DeviceType')->findAll(null, ['id', 'name']);
            if($rows) {
                $cache = [];
                foreach ($rows as $row) {
                    $cache[$row['id']] = $row['name'];
                }
                
                getRedis()->set('device.type', json_encode($cache));
            }
        });
    }
    
    public function getDeviceTypeList() {
        $id = intval($this->GET['id'] ?? 0);
        $typeList = model('DeviceType')->findAll(['parent_id' => $id]);
        return $this->successResult($typeList);
    }
    
    public function typeList() {
        $typeList = model('DeviceType')->findAll();
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
            
            $model = model('DeviceType');
            
            if($model->isExistsByWhere(['name' => $data['name'], 'parent_id' => $parentId])) {
                return $this->errorResult('已存在相同的分类');
            }
            
            if(!empty($params['parentId'])) {
                $row = $model->findOne(['id' => $params['parentId']], 'level');
                if(!empty($row)) {
                    $data['parent_id'] = $params['parentId'];
                    $data['level'] = $row['level'] + 1;
                }else {
                    return $this->errorResult();
                }
            }else {
                $data['level'] = 0;
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
            
            $model = model('DeviceType');
            
            $row = $model->findOne(['id' => $id], ['name', 'parent_id']);
            if(empty($row)) return $this->errorResult();
            if($row['name'] == $name) return $this->successResult();
            
            if($model->isExistsByWhere(['name' => $name, 'parent_id' => $row['parent_id']])) {
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
            $model = model('DeviceType');
            if($model->isExistsByWhere(['parent_id' => $id])) {
                return $this->errorResult('该分类下存在子分类，不能删除');
            }
            
            if($model->delete(['id' => $id])) {
                self::updateDeviceTypeCache();
                getTask()->async(function() use($id) {
                    model('Device')->modify(['device_type' => $id], ['device_type' => 0]);
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