<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

class AppController extends Controller
{
    protected $statusCode;
    protected $message;
    protected $queryMessage;
    protected $queryStatus;
    protected $response;

    public function __construct()
    {
        $this->statusCode=200;
        $this->queryStatus=500;
        $this->response=false;
        $this->message = '';
        $this->queryMessage = 'Something went wrong. Please Contact Quiz Administrator';
    }
}
