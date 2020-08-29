<?php
namespace App\HttpController\Api;
class Charge extends Base {
    public function doCharge() {
        $params = $this->POST;
        $deviceId = intval($params['deviceId'] ?? 0);
        if($deviceId <= 0) return $this->errorResult();
        
        $portId = intval($params['portId'] ?? 0);
        if($portId <= 0) return $this->errorResult();
        
        $priceId = intval($params['priceId'] ?? 0);
        if($priceId <= 0) return $this->errorResult();
        
        $quantity = floatval($params['quantity'] ?? 0); //充电数量
        if($quantity <= 0) return $this->errorResult();
        
        $amount = floatval($params['amount'] ?? 0); //充电金额，用来做校验
        if($amount < 0) return $this->errorResult();
        
        $payType = intval($params['payType'] ?? 0);
        if(!in_array($payType, [1, 2])) { // 1:余额，2：在线支付
            return $this->errorResult();
        }
        
        $device = model('Device')->findOne(['device_id' => $deviceId], ['status', 'price_t', 'price_w', 'heart_time']);
        if(empty($device)) return $this->errorResult();
        if($device['status'] != 1) return $this->errorResult('设备暂停使用');
        
        $port = model('DevicePort')->findOne(['port_id' => $portId], ['device_id', 'port_no', 'status']);
        if(empty($port) || $port['device_id'] != $deviceId) return $this->errorResult();
        if($port['status'] != 0) return $this->errorResult('端口暂不可用，请选择其它空闲端口');
        
        $time = time();
        
        if($device['heart_time'] < ($time - 10 * 60)) { //判断为掉线
            return $this->errorResult('设备已掉线');
        }
        
        $chargePrice = model('ChargePrice')->findOne(['price_id' => $priceId], ['options', 'charge_type', 'charge_unit']);
        if(empty($chargePrice) || empty($chargePrice['options'])) { //查不到价格，或者未设置选项
            return $this->errorResult('设备暂不支持扫码充电');
        }
        
        if($chargePrice['charge_type'] == 1) { //计时
            if($device['price_t'] != $priceId) return $this->errorResult(); //价格Id对不上
        }else { //计量
            if($device['price_w'] != $priceId) return $this->errorResult(); //价格Id对不上
        }
        
        //开始校验价格
        $checkPrice = false;
        $chargePriceOptions = json_decode($chargePrice['options'], true);
        if(empty($chargePriceOptions)) return $this->errorResult('设备暂不支持扫码充电');
        foreach($chargePriceOptions as $option) {
            if($option['quantity'] == $quantity && $option['amount'] == $amount) { //校验通过
                $checkPrice = true;
                break;
            }
        }
        if($chargePrice == false) return $this->errorResult();
        
        $orderNo = buildOrderNo($this->userId);
        $orderData = [
            'order_no' => $orderNo,
            'user_id' => $this->userId,
            'device_id' => $deviceId,
            'port_no' => $port['port_no'],
            'quantity' => $quantity,
            'amount' => $amount,
            'charge_type' => $chargePrice['charge_type'],
            'charge_unit' => $chargePrice['charge_unit'],
            'pay_type' => $payType,
            'create_time' => $time
        ];
        
        if($amount == 0) { //免费充电
            $orderData['pay_status'] = 1;
            //开始充电
        }else {
            if($payType == 1) { //余额支付
                $wallet = model('User')->findOne(['user_id' => $this->userId], ['balance']);
                if(empty($wallet) || $wallet['balance'] < $amount) return $this->errorResult('余额不足');
                
                
                
                
            }else {
                
            }
        }
        
    }
}











