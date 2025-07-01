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

    // Default page where all the projects categories will be shown
    public function projects(): string
    {   
        $data = [
            'content' => 'content/projects/common_projects'
        ];
        // return view('content/home/home');
        return view('common/body' , $data);
    }
}
