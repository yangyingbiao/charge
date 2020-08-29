<?php
namespace App\HttpController\Admin;
class Permission extends Base {
    public function roleList() {
        $params = $this->GET;
        $pageNo = intval($params['pageNo'] ?? 1);
        $pageSize = intval($params['pageSize'] ?? 15);
        $offset = ($pageNo - 1) * $pageSize;

        $where = 'user_id = ' . $this->userId;

        $name = $params['name'] ?? '';
        if($name !== '') {
            $where .= ' and name like %' . $name . '%';
        }

        $model = model('permissionRole');
        $model->where($where)->limit($offset, $pageSize)->withTotalCount();
        $list = $model->findAll(null, ['id', 'name', 'remark', 'status', 'create_time']);
        $total = $model->lastQueryResult()->getTotalCount();
        
        return $this->successResult(['pageNo' => $pageNo, 'pageSize' => $pageSize, 'totalCount' => $total, 'list' => $list]);
    }
    
    public function addRole() {
        return $this->save($this->POST);
    }
    
    public function editRole() {
        return $this->save($this->POST);
    }

    //增加角色
    private function save($params) {
        $data = [];
        
        $data['name'] = trim($params['name'] ?? '');
        $data['permission'] = $params['permission'] ?? [];
        if($data['name'] === '' || empty($data['permission']) || !is_array($data['permission'])) return $this->errorResult();
        
        $data['remark'] = trim($params['remark'] ?? '');
        $data['status'] = intval($params['status'] ?? 0);
        if($data['status'] !== 0 && $data['status'] !== 1) return $this->errorResult();
        
        ksort($data['permission']);
        $data['permission'] = json_encode($data['permission']);

        $id = intval($params['id'] ?? 0);
        $time = time();
        $model = model('permissionRole');
        
        
        
        if($id == 0) { //新增
            if($model->isExistsByWhere(['name' => $data['name'], 'user_id' => $this->userId])) return $this->errorResult('角色名称已经存在');
            $data['user_id'] = $this->userId;
            $data['create_time'] = $time;
            $id = $model->add($data);
            if($id) {
                return $this->successResult(['id' => $id, 'create_time' => $time]);
            }else {
                return $this->errorResult('失败');
            }
        }else { //编辑
            $row = $model->findOne(['id' => $id], ['name', 'permission', 'user_id', 'remark', 'status']);
            if(empty($row) || $row['user_id'] != $this->userId) return $this->errorResult();
            
            $updateData = [];
            foreach($row as $key => $val) {
                if(isset($data[$key]) && $val != $data[$key]) {
                    $updateData[$key] = $data[$key];
                }
            }

            if(empty($updateData)) return $this->successResult();

            if(isset($updateData['name'])) { //有修改名称
                if($model->isExistsByWhere(['name' => $updateData['name'], 'user_id' => $this->userId])) return $this->errorResult('角色名称已经存在');
            }

            if($model->modify(['id' => $id], $updateData)) {
                $this->successResult('修改成功');
            }else {
                $this->errorResult();
            }

        }
        
    }
    
    public function changeStatus() {
        $id = intval($this->POST['id'] ?? 0);
        $model = model('permissionRole');
        $row = $model->findOne(['id' => $id], ['status', 'user_id']);
        if(empty($row) || $row['user_id'] != $this->userId) return $this->errorResult();
        if($model->modify(['id' => $id], ['status' => $row['status'] == 1 ? 0 : 1])) {
            return $this->successResult('修改成功');
        }else {
            return $this->errorResult();
        }
    }
    
    public function deleteRole() {
        $id = intval($this->POST['id'] ?? 0);
        $model = model('permissionRole');
        $row = $model->findOne(['id' => $id], ['status', 'user_id']);
        if(empty($row) || $row['user_id'] != $this->userId) return $this->errorResult();
        if($model->delete(['id' => $id])) {
            return $this->successResult('删除成功');
        }else {
            return $this->errorResult();
        }
    }
    
    public function roleOption() {
        $roleList = model('permissionRole')->findAll(['user_id' => $this->userId, 'status' => 1], ['name', 'id']);
        return $this->successResult($roleList);
    }
    
    public function roleEditInfo() {
        $id = intval($this->GET['id'] ?? 0);
        $row = model('permissionRole')->findOne(['id' => $id], ['name', 'status', 'permission', 'remark', 'user_id']);
        if(empty($row) || $row['user_id'] != $this->userId) return $this->errorResult();
        return $this->successResult($row);
    }
    
}















