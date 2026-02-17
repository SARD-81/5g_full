<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    protected $statusCode;

    public function respondSuccess(string $message, $data)
    {
        return $this->setStatusCode(200)->respond($message, true, $data);
    }
 
    public function respondInternalError($message, $data = null)
    {
        return $this->setStatusCode(500)->respond($message, false);
    }

    public function respondNotFound($message)
    {
        return $this->setStatusCode(404)->respond($message);
    }

    public function respondCreated(string $message, $data)
    {
        return $this->setStatusCode(201)->respond($message, true, $data);
    }

    public function respondUnprocessable($message)
    {
        return $this->setStatusCode(422)->respond($message);
    }

    public function responBadRequest($message)
    {
        return $this->setStatusCode(400)->respond($message);
    }

    private function respond($message = '', bool $isSuccess = false, $data = null)
    {
        if ($this->statusCode == 404 || $this->statusCode == 500 || $this->statusCode == 422 || $this->statusCode == 400) {

            $responseData = [
                'success' => $isSuccess,
                'msg' => $message,
            ];

            return response()->json($responseData)->setStatusCode($this->getStatusCode());
        }

            $responseData = [
                'success' => $isSuccess,
                'msg' => $message,
                'data' => $data
            ];
            
            return response()->json($responseData)->setStatusCode($this->getStatusCode());
        
    }

    private function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;  
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}