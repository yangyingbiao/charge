<?php
namespace App\HttpController\Admin;
class Device extends Base {
    
    public function deviceList() {
        $params = $this->GET;
        $pageNo = intval($params['pageNo'] ?? 1);
        $pageSize = intval($params['pageSize'] ?? 15);
        $offset = ($pageNo - 1) * $pageSize;
        
        $where = '1=1';
        
        $model = model('device');
        $model->where($where)->limit($offset, $pageSize)->withTotalCount();
        $deviceList = $model->findAll();
        if(!empty($deviceList)) {
            
            $priceKeyList = []; //把id转成键
            
            $priceIdList = [];
            foreach ($deviceList as $val) {
                array_push($priceIdList, $val['price_t'], $val['price_w']);
            }
            $priceIdList = array_unique($priceIdList);
            if(in_array(0, $priceIdList)) {
                sort($priceIdList);
                array_shift($priceIdList);
            }
            if(!empty($priceIdList)) {
                $priceList = model('chargePrice')->findAll($priceIdList, ['price_id', 'price_name', 'options', 'power_class']);
                foreach($priceList as $price) {
                    $priceKeyList[$price['price_id']] = ['name' => $price['price_name'], 'options' => json_decode($price['options'], true), 'power_class' => json_decode($price['power_class'], true)];
                }
            }

            foreach($deviceList as $k => $device) {
                $deviceList[$k]['price_t_data'] = $priceKeyList[$device['price_t']] ?? null;
                $deviceList[$k]['price_w_data'] = $priceKeyList[$device['price_w']] ?? null;
            }
            
            
        }
        
        $total = $model->lastQueryResult()->getTotalCount();
        
        return $this->successResult(['pageNo' => $pageNo, 'pageSize' => $pageSize, 'totalCount' => $total, 'list' => $deviceList]);
    }
    
    public function addDevice() {
       $params = $this->POST;
       if(isset($params['id'])) return $this->errorResult();
       return $this->save($params);
    }
    
    public function editDevice() {
        $params = $this->POST;
        if(!isset($params['id'])) return $this->errorResult();
        return $this->save($params);
    }
    
    private function save($params) {
        $data = [];
        
        if(isset($params['deviceType']) && $params['deviceName'] !== '') { //设备类型
            $data['device_type'] = intval($params['deviceType']);
            
            //校验设备类型有无存在
            if(model('deviceType')->isExistsByWhere(['id' => $data['device_type']]) == false) return $this->errorResult('设备类型不存在');
            
        }
        
        if(isset($params['deviceName']) && $params['deviceName'] !== '') {
            $data['device_name'] = trim($params['deviceName']);
        }
        
        if(isset($params['simIccid']) && $params['simIccid'] !== '') {
            $data['sim_iccid'] = trim($params['simIccid']);
        }
        
        if(isset($params['portCount']) && $params['networkType'] !== '') { //端口数
            $data['port_count'] = intval($params['portCount']);
            if($data['port_count'] < 0) return $this->errorResult('请填写合适的端口数');
        }
        
        if(isset($params['networkType']) && $params['networkType'] !== '') {
            $data['network_type'] = intval($params['networkType']);
            //校验通讯类型有无存在
            if(model('NetworkType')->isExistsByWhere(['id' => $data['network_type']]) == false) return $this->errorResult('通讯类型不存在');
        }
        
        if(isset($params['priceT']) && $params['priceT'] !== '') { //计时价格
            $data['price_t'] = intval($params['priceT']);
            //校验充电价格有无存在
            if(model('chargePrice')->isExistsByWhere(['price_id' => $data['price_t']]) == false) return $this->errorResult('计时价格不存在');
        }
        
        if(isset($params['priceW']) && $params['priceW'] !== '') { //计量价格
            $data['price_w'] = intval($params['priceW']);
            //校验充电价格有无存在
            if(model('chargePrice')->isExistsByWhere(['price_id' => $data['price_w']]) == false) return $this->errorResult('计量价格不存在');
        }
        
        if(isset($params['area'])) {
            $data['province_id'] = isset($params['area'][0]) ? intval($params['area'][0]) : 0; //
            $data['city_id'] = isset($params['area'][1]) ? intval($params['area'][1]) : 0; //
            $data['district_id'] = isset($params['area'][2]) ? intval($params['area'][2]) : 0; //
            
            if($data['province_id'] > 0) {
                $areaModel = model('Area');
                $data['province'] = $areaModel->where(['id' => $data['province_id']])->val('name');
                if(empty($data['province'])) return $this->errorResult('省份不存在');
                
                if($data['city_id'] > 0) {
                    $data['city'] = $areaModel->where(['id' => $data['city_id']])->val('name');
                    if(empty($data['city'])) return $this->errorResult('城市不存在');
                    
                    if($data['district_id'] > 0){
                        $data['district'] = $areaModel->where(['id' => $data['district_id']])->val('name');
                        if(empty($data['district'])) return $this->errorResult();
                    }
                }
            }
        }
        
        $data['address'] = isset($params['address']) ? trim($params['address']) : '';
       
        $data['status'] = intval($data['status'] ?? 1);
        if(in_array($data['status'], [0, 1])) return $this->errorResult();
        
        $deviceId = intval($params['id'] ?? 0);
        if($deviceId < 0) return $this->errorResult();

        $time = time();
        $model = model('Device');
        
        if($deviceId == 0) { //新增
            if(empty($data['device_type'])) return $this->errorResult('请选择设备类型');
            if(empty($data['sim_iccid'])) return $this->errorResult('请填写sim iccid');
            
            //查看sim是否已经存在
            if($model->isExistsByWhere(['sim_iccid' => $data['sim_iccid']]) == true) {
                return $this->errorResult('已存在相同的sim号');
            }
            
            $data['create_time'] = $time;
            
            try {
                if($model->startTrans() == false) return $this->errorResult('事务启动失败');
                
                $deviceId = $model->add($data);
                if(empty($deviceId)) {
                    $model->rollback();
                    return $this->errorResult('新增设备失败');
                }
                
                if($data['port_count'] > 0) {
                    $portData = [];
                    for($i = 1; $i <= $data['port_count']; $i ++){
                        array_push($portData, ['device_id' => $deviceId, 'port_no' => $i]);
                    }
                    
                    $rs = model('DevicePort')->addAll($portData, true, false);
                    if(!$rs) {
                        $model->rollback();
                        return $this->errorResult('新增设备失败');
                    }
                }
                
                $model->commit();
                
            } catch (\Exception $e) {
                $model->rollback();
                return $this->errorResult($e->getMessage());
            }
            
            return $this->successResult('新增成功');
        }else { //编辑
            $row = $model->findOne(['device_id' => $deviceId]);
            if(empty($row)) return $this->errorResult('数据不存在');
            
            $updateData = [];
            
            foreach ($data as $k => $val) {
                if(isset($row[$k]) && $val != $row[$k]) {
                    $updateData[$k] = $val;
                }
            }
            
            if(empty($updateData)) return $this->successResult('修改成功');
            
            $data['update_time'] = $time;
            
            if(isset($updateData['sim_iccid'])) {
                if($updateData['sim_iccid'] === '') return $this->errorResult('请填写sim iccid');
                if($model->isExistsByWhere(['sim_iccid' => $updateData['sim_iccid']]) == true) return $this->errorResult('已存在相同的sim号');
            }
            
            if(isset($updateData['port_count'])) { //端口数变了
                try {
                    if($model->startTrans() == false) return $this->errorResult('启动事务失败');
                    $portModel = model('DevicePort');
                    
                    $offset = $updateData['port_count'] - $row['port_count'];
                    
                    if($offset > 0) { //增加端口
                        $portData = [];
                        for($i = 1; $i <= $offset; $i ++){
                            array_push($portData, ['device_id' => $deviceId, 'port_no' => $row['port_count'] + $i]);
                        }
                        
                        $rs = $portModel->addAll($portData, true, false);
                        if(!$rs) {
                            $model->rollback();
                            return $this->errorResult('新增端口失败');
                        }
                    }else{ //减少端口
                        $offset = abs($offset);
                        $portNos = [];
                        
                        for($i = 0; $i < $offset; $i ++) {
                            array_push($portNos, $row['port_count'] - $i);
                        }
                        $rs = $portModel->delete('device_id=' . $deviceId . ' and port_no in(' . implode(',', $portNos) . ')');
                        if(!$rs) {
                            $model->rollback();
                            return $this->errorResult('减少端口失败');
                        }
                    }

                    if($model->modify(['device_id' => $deviceId], $updateData) == false) {
                        $model->rollback();
                        return $this->errorResult('修改设备信息失败');
                    }
                    
                    $model->commit();
                    
                    return $this->successResult('修改成功');
                    
                } catch (\Exception $e) {
                    $model->rollback();
                    return $this->errorResult($e->getMessage());
                }
                
                
            }else {
                return $model->modify(['device_id' => $deviceId], $updateData) == true ?  $this->successResult('修改成功') : $this->errorResult('修改失败');
            }
            
        }
    }
    
    public function editInfo() {
        $id = intval($this->GET['id'] ?? 0);
        if($id <= 0) return $this->errorResult();
        
        $device = model('device')->findOne(['device_id' => $id]);
        if(empty($device)) return $this->errorResult();
        
        $deviceTypeList = model('deviceType')->findAll();
        $deviceTypeList = array_reverse($deviceTypeList);
        
        $currentType = $device['device_type'];
        $device['device_type'] = [];
        
        foreach($deviceTypeList as $type) {
            if($type['id'] == $currentType) {
                array_push($device['device_type'], $type['id']);
                $currentType = $type['parent_id'];
            }
        }
        
        $device['device_type'] = array_reverse($device['device_type']);
        
        $device['area'] = [];
        if($device['province_id']) {
            array_push($device['area'], $device['province_id']);
            if($device['city_id']) {
                array_push($device['area'], $device['city_id']);
                if($device['district_id']) {
                    array_push($device['area'], $device['district_id']);
                }
            }
        }
        
        return $this->successResult($device);
        
    }
    
    public function deleteDevice() {
        $id = intval($this->POST['id'] ?? 0);
        if($id <= 0) return $this->errorResult();

        $model = model('Device');
        
        try {
            if($model->startTrans() == false) return $this->errorResult('事务启动失败');
            
            $row = model('device')->lock(['device_id' => $id]);
            if(empty($row)) {
                $model->rollback();
                return $this->errorResult('数据不存在');
            }
            
            if($model->delete(['device_id' => $id]) == false) {
                $model->rollback();
                return $this->errorResult('删除设备失败');
            }
            
            if(model('DevicePort')->delete(['device_id' => $id]) == false) {
                $model->rollback();
                return $this->errorResult('删除端口失败');
            }
            
            $rs = model('Recycle')->add(['pk' => $id, 'type' => 1, 'symbol' => $row['sim_iccid'], 'data' => json_encode($row), 'create_time' => time()]);
            if(!$rs) {
                $model->rollback();
                return $this->errorResult('设备进入回收站失败');
            }
            
            $model->commit();
            
            return $this->successResult();
            
        } catch (\Exception $e) {
            $model->rollback();
            return $this->errorResult('删除失败');
        }
        
    }
}










