<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

class AppController extends Controller
{
    protected $status;
    protected $queryMessage;


    public function __construct()
    {

        $this->status=200;
        $this->queryMessage = 'Something went wrong. Please Contact Quiz Administrator';
    }
}
