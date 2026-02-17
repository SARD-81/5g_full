<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
//    $mergedFileName = 'sdsdf.lok';
//    return url("/captures/{$mergedFileName}");


//    $server['ip'] = '192.168.0.79';
//    $username = 'bbdh';
//    $password = '1234';
//    $port = '22';
//
//    $sshHelper = new sshHelper($server, $username, $password, $port, 7);
//    return $sshHelper->testConnection();


    return view('welcome');
    
});
