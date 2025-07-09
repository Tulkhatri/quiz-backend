<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

class AppController extends Controller
{
    protected $status;
    protected $message;
    protected $queryMessage;
    protected $response;

    public function __construct()
    {
        $this->status=200;
        $this->message = '';
        $this->response = false;
        $this->queryMessage = 'Something went wrong. Please Contact Quiz Administrator';
    }
}
