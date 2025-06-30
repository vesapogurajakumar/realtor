<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {   
        $data = [
            'content' => 'content/home/home'
        ];
        // return view('content/home/home');
        return view('common/body' , $data);
    }
}
