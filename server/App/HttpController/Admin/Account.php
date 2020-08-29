<?php
namespace App\HttpController\Admin;
class Account extends Base {
    public function accountList() {
        $params = $this->GET;
        $pageNo = intval($params['pageNo'] ?? 1);
        $pageSize = intval($params['pageSize'] ?? 15);
        $offset = ($pageNo - 1) * $pageSize;
        
        $where = 'super = 0 and type = ' . $this->type;
        
        $account = $params['account'] ?? '';
        if($account !== '') {
            $where .= ' and account = "' . $account . '"';
        }
        
        $model = model('admin');
        $model->where($where)->limit($offset, $pageSize)->withTotalCount();
        $list = $model->findAll(null, ['user_id', 'account', 'name', 'role', 'remark', 'status', 'create_time']);
        if(!empty($list)) {
            $roleIds = [];
            foreach ($list as &$val) {
                if(!empty($val['role'])) {
                    $val['role'] = json_decode($val['role'], true);
                    $roleIds = array_merge($roleIds, $val['role']);
                }
            }
            
            $roles = model('permissionRole')->findAll(array_unique($roleIds), ['name', 'id', 'status']);
            foreach ($list as $key => &$val) {
                $val['roleName'] = [];
                if(!empty($val['role'])) {
                    foreach ($val['role'] as $k => $id) {
                        foreach ($roles as $role) {
                            if($role['status'] != 1) continue;
                            if($role['id'] == $id) {
                                array_push($val['roleName'], $role['name']);
                                break;
                            }
                        }
                    }
                }
            }
            
        }
        $total = $model->lastQueryResult()->getTotalCount();
        
        return $this->successResult(['pageNo' => $pageNo, 'pageSize' => $pageSize, 'totalCount' => $total, 'list' => $list]);
    }
    
    public function addAccount() {
        $this->save($this->POST);
    }
    
    public function editAccount() {
        $this->save($this->POST);
    }
    
    public function account() {
        $id = intval($this->GET['id'] ?? 0);
        $row = model('admin')->findOne(['user_id' => $id], ['account', 'name', 'status', 'role', 'remark', 'creater']);
        if(empty($row)) return $this->errorResult();
        unset($row['creater']);
        return $this->successResult($row);
    }
    
    private function save($params) {
        $data = [];
        $data['account'] = trim($params['account'] ?? '');
        $data['name'] = trim($params['name'] ?? '');
        $data['role'] = $params['role'] ?? [];
        if($data['account'] === '' || $data['account'] === '' || empty($data['role']) || !is_array($data['role'])) return $this->errorResult();
        
        $data['remark'] = trim($params['remark'] ?? '');
        $data['status'] = intval($params['status'] ?? 0);
        if($data['status'] !== 0 && $data['status'] !== 1) return $this->errorResult();
        
        ksort($data['role']);
        $data['role'] = json_encode($data['role']);
        
        $id = intval($params['id'] ?? 0);
        $time = time();
        $model = model('admin');
        
        if($id == 0) { //新增
            if($model->isExistsByWhere(['account' => $data['account']])) return $this->errorResult('账号已存在');
            $data['creater'] = $this->userId;
            $data['type'] = $this->user['type'];
            $data['create_time'] = $time;
            
            $salt = uniqid();
            $data['salt'] = substr($salt, strlen($salt) -7 , 6);
            $data['password'] = md5(md5('123456') . $data['salt']);
            $id = $model->add($data);
            if($id) {
                return $this->successResult(['user_id' => $id, 'create_time' => $time, 'type' => $data['type']]);
            }else {
                return $this->errorResult('失败');
            }
        }else { //编辑
            $row = $model->findOne(['user_id' => $id], ['name', 'account', 'role', 'creater', 'status', 'remark']);
            if(empty($row)) return $this->errorResult();
            
            $updateData = [];
            foreach($row as $key => $val) {
                if(isset($data[$key]) && $val != $data[$key]) {
                    $updateData[$key] = $data[$key];
                }
            }
            
            if(empty($updateData)) return $this->successResult();
            
            if(isset($updateData['account'])) { //修改账号
                if($model->isExistsByWhere(['account' => $updateData['account']])) return $this->errorResult('账号已存在');
            }
            
            if($model->modify(['user_id' => $id], $updateData)) {
                $this->successResult('修改成功');
            }else {
                $this->errorResult();
            }
            
        }
        
    }
    
    public function changeStatus() {
        $id = intval($this->POST['id'] ?? 0);
        $model = model('admin');
        $row = $model->findOne(['user_id' => $id], ['status', 'creater']);
        if(empty($row)) return $this->errorResult();
        if($model->modify(['user_id' => $id], ['status' => $row['status'] == 1 ? 0 : 1])) {
            if($row['status'] == 1) { //转成禁用
                getTask()->async(function() use($id) {
                    getRedis()->del('user.' . $id);
                });
            }
            return $this->successResult('修改成功');
        }else {
            return $this->errorResult();
        }
    }
    
    public function deleteAccount() {
        $id = intval($this->POST['id'] ?? 0);
        $model = model('admin');
        $row = $model->findOne(['user_id' => $id], ['status', 'creater']);
        if(empty($row)) return $this->errorResult();
        if($model->delete(['user_id' => $id])) {
            return $this->successResult('删除成功');
        }else {
            return $this->errorResult();
        }
    }
    
    public function resetPassword() { //重置密码
        $password = trim($this->POST['password'] ?? '');
        if($password === '') return $this->errorResult('请输入密码');
        if(stripos($password, ' ') !== false) return $this->errorResult('密码不可以包含空格');
        if(strlen($password) > 16 || strlen($password) < 6) return $this->errorResult('密码长度必须6-16个字符');
        
        $userId = intval($this->POST['id'] ?? 0);
        
        $model = model('admin');
        $user = $model->findOne(['user_id' => $userId], ['password', 'salt']);
        if(empty($user)) return $this->errorResult();
        if(md5(md5($password) . $user['salt']) === $user['password']) return $this->errorResult('密码不可以和旧密码一样');
        
        $salt = uniqid();
        $data['salt'] = substr($salt, strlen($salt) -7 , 6);
        $data['password'] = md5(md5($password) . $data['salt']);
        
        if($model->modify(['user_id' => $userId], $data)) {
            getTask()->async(function() use($userId) {
                getRedis()->del('user.' . $userId);
            });
            return $this->successResult('重置密码成功');
        }else {
            return $this->errorResult();
        }
    }
    
}















