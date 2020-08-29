<?php
namespace App\HttpController\Admin;
class ChargePrice extends Base {
    public function getPriceList() {
        $type = intval($this->GET['type'] ?? 1);
        $typeList = model('ChargePrice')->findAll(['user_id' => $this->userId, 'charge_type' => $type]);
        return $this->successResult($typeList);
    }
    
    public function priceList() { //列表
        $params = $this->GET;
        $pageNo = intval($params['pageNo'] ?? 1);
        $pageSize = intval($params['pageSize'] ?? 15);
        $offset = ($pageNo - 1) * $pageSize;
        
        $where = 'user_id = ' . $this->userId;
        
        $model = model('ChargePrice');
        $model->where($where)->limit($offset, $pageSize)->withTotalCount();
        $list = $model->findAll(null);

        $total = empty($list) ? 0 : $model->lastQueryResult()->getTotalCount();
        
        return $this->successResult(['pageNo' => $pageNo, 'pageSize' => $pageSize, 'totalCount' => $total, 'list' => $list]);
    }
    
    public function addPrice() {
        $params = $this->POST;
        if(isset($params['id'])) {
            return $this->errorResult();
        }
        
        return $this->_save($params);
    }
    
    public function editPrice() {
        $params = $this->POST;
        if(!isset($params['id'])) {
            return $this->errorResult();
        }
        
        return $this->_save($params);
    }
    
    private function _save($params) {
        $id = intval($params['id'] ?? 0);
        if($id < 0) return $this->errorResult();
        
        $data = [];
        if(isset($params['priceName'])) {
            $data['price_name'] = trim($params['priceName']);
        }
        
        if(isset($params['chargeType'])) { //传入的计费方式不存在
            $data['charge_type'] = intval($params['chargeType']);
            if(!isset((getConfig('SYS.price_charge_type'))[$data['charge_type']])) {
                return $this->errorResult();
            }
        }
        
        if(isset($params['chargeUnit'])) { //计费单位
            $data['charge_unit'] = floatval($params['chargeUnit']);
            if($data['charge_unit'] < 0) return $this->errorResult();
        }
        
        if(isset($params['maxPower'])) { //最大功率
            $data['max_power'] = intval($params['maxPower']);
            if($data['max_power'] < 0) return $this->errorResult();
        }
        
        if(isset($params['criticalPower'])) { //临界功率
            $data['critical_power'] = intval($params['criticalPower']);
            if($data['critical_power'] < 0) return $this->errorResult();
        }
        
        if(isset($params['checkTime'])) { //检测时间
            $data['check_time'] = intval($params['checkTime']);
            if($data['check_time'] < 0) return $this->errorResult();
        }
        
        if(isset($params['prepayment'])) { //预付费
            $data['prepayment'] = intval($params['prepayment']);
            if($data['prepayment'] < 0) return $this->errorResult();
        }
        
        if(isset($params['powerClass']) && is_array($params['powerClass'])) {
            $data['power_class'] = [];
            foreach ($params['powerClass'] as $item) {
                try {
                    $quantity = floatval($item['quantity'] ?? 0);
                    $power = floatval($item['power'] ?? 0);
                    if($quantity <= 0 || $power <= 0) return $this->errorResult('功率分档不能为0');
                    array_push($data['power_class'], ['power' => $power, 'quantity' => $quantity]);
                } catch (\Exception $e) {
                    return $this->errorResult();
                }
            }
            
            if(empty($data['power_class']) && $id == 0) {
                unset($data['power_class']);
            }else {
                $data['power_class'] = json_encode($data['power_class']);
            }
        }
        
        if(isset($params['options']) && is_array($params['options'])) {
            $data['options'] = [];
            foreach ($params['options'] as $item) {
                try {
                    $amount = floatval($item['amount'] ?? 0);
                    $quantity = floatval($item['quantity'] ?? 0);
                    if($amount < 0) return $this->errorResult('预设金额不能小于0');
                    if($quantity <= 0) return $this->errorResult('预设充电时长/度数必须大于0');
                    array_push($data['options'], ['amount' => $amount, 'quantity' => $quantity]);
                } catch (\Exception $e) {
                    return $this->errorResult();
                }
            }
            if(empty($data['options']) && $id == 0) {
                unset($data['options']);
            }else {
                $data['options'] = json_encode($data['options']);
            }
        }
        
        $time = time();
        
        $model = model('ChargePrice');

        if($id == 0) { //新增
            $data['user_id'] = $this->userId;
            $data['create_time'] = $time;
            $id = $model->add($data);
            if(!$id) {
                return $this->errorResult('保存失败');
            }else {
                return $this->successResult();
            }
        }else {
            if(isset($data['charge_type'])) {
                unset($data['charge_type']); //编辑不能修改计费类型
            }
            
            $row = $model->findOne(['price_id' => $id]);
            if(empty($row) || $row['user_id'] != $this->userId) return $this->errorResult('数据不存在');
            $updateData = [];
            foreach ($data as $key => $val) {
                if(array_key_exists($key, $row) && $val != $row[$key]) {
                    $updateData[$key] = $val;
                }
            }
  
            if(empty($updateData)) return $this->successResult('保存成功');
            
            $updateData['update_time'] = $time;
            
            if($model->modify(['price_id' => $id], $updateData) == false) return $this->errorResult('保存失败');
            $this->successResult('保存成功');
        }
    }
    
    public function deletePrice() {
        try {
            $id = intval($this->POST['id'] ?? 0);
            $model = model('chargePrice');
            $row = $model->findOne(['price_id' => $id], ['user_id', 'charge_type']);
            if(empty($row) || $row['user_id'] != $this->userId) return $this->errorResult();
            
            if($model->delete(['price_id' => $id])) {
                getTask()->async(function() use($id, $row) {
                    if($row['charge_type'] == 1) { //计时
                        model('Device')->modify(['price_t' => $id], ['price_t' => 0]);
                    }else { //计量
                        model('Device')->modify(['price_w' => $id], ['price_w' => 0]);
                    }
                });
                return $this->successResult();
            }
            
        } catch (\Exception $e) {
        }
        
        return $this->errorResult();
    }
    
    public function priceDetail() {
        $id = intval($this->GET['id'] ?? 0);
        if($id <= 0) return $this->errorResult();
        
        $row = model('chargePrice')->findOne(['price_id' => $id]);
        if(empty($row) || $row['user_id'] != $this->userId) return $this->errorResult();
        
        return $this->successResult($row);
    }
}










