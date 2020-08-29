<?php
namespace App\Model;

use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\AbstractModel;

class Base extends AbstractModel {
    protected $pkName = 'id';
    public function getLastSql() {
        return $this->lastQuery()->getLastQuery();
    }

    public function add(array $data) {
        return $this->data($data)->save();
    }

    public function addAll(array $data, $replace = true, $transaction = true) {
        try{
            $this->saveAll($data, $replace, $transaction);
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function modify($where = null, array $data = []) {
        try {
            return $this->update($data, $where);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete($where) {
        try {
            return $this->destroy($where);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function findOne($where = null, $field = '*') {
        $res = $this->field($field)->get($where);
        if(!empty($res)) {
            return $res->toRawArray();
        }else {
            return null;
        }
    }

    public function findAll($where = null, $field = '*', $order = '') {
        $this->field($field);
        if($order) {
            $this->order($order);
        }
        $res = $this->all($where);

        $result = [];

        if(!empty($res)) {
            foreach($res as $item) {
                if(!empty($item)) {
                    array_push($result, $item->toRawArray());
                }
            }
        }
        
        return $result;
    }
    
    public function isExistsByWhere($where = null) {
        $res = $this->field($this->pkName)->get($where);
        return !empty($res);
    }

    
}