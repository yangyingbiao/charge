<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;

class Common extends Controller {
    protected $POST = [];
    protected $GET = [];

    protected function onRequest(?string $action) : ?bool {
        $request = $this->request();
        $this->POST = $request->getParsedBody();

        $raw = $request->getBody()->__toString();
        if(!empty($raw)) {
            $raw_array = json_decode($raw, true);
            if(!empty($raw_array)) {
                $this->POST = array_merge($this->POST, $raw_array);
            }
        }

        return true;
        
    }

    protected function writeJson($statusCode = 200, $result = null, $msg = null) {
        if (!$this->response()->isEndResponse()) {
            $data = Array(
                'code' => $statusCode,
                'data' => $result,
                'msg' => $msg
            );
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        } else {
            return false;
        }
    }

    protected function successResult($data = null, $msg = 'success', $code = 200) {
        $this->writeJson($code, $data, $msg);
        $this->response()->end();
        
        return true;
    }

    protected function errorResult($msg = '请求失败', $code = 400, $data = null) {
        $this->writeJson($code, $data, $msg);
        $this->response()->end();
        
        return false;
    }
}