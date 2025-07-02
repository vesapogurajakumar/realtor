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
         $data['latest_feed'] = [
            'section1' => [
                'src' => './assets/images/blog-1.png',
                'address' => 'Belmont Gardens, Chicago',
                'discription' => 'The Most Inspiring Interior Design Of 2021',
                'discription2' => 'Beautiful Huge 1 Family House In Heart Of Westbury. Newly Renovated With New Wood',
                'date' => 'Apr 27, 2022',
                'price' => '$34,9001',
                'interior_url' => 'www.facebook.com',
                'features' => [
                    'key1' => 'Bedrooms',
                    'key2' => 'Bathrooms',
                    'key3' => 'Square Ft',
                    'key1value' => '3',
                    'key2value' => '2',
                    'key3value' => '3450',

                ],
            ],
            'section2' => [
                'src' => './assets/images/blog-2.jpg',
                'address' => 'Belmont Gardens, Chicago2',
                'discription' => 'The Most Inspiring Interior Design Of 2021',
                'discription2' => 'Beautiful Huge 1 Family House In Heart Of Westbury. Newly Renovated With New Wood2',
                'date' => 'Apr 27, 2022',
                'price' => '$34,9002',
                'interior_url' => 'www.facebook.com',
                'features' => [
                    'key1' => 'Bedrooms',
                    'key2' => 'Bathrooms',
                    'key3' => 'Square Ft',
                    'key1value' => '5',
                    'key2value' => '2',
                    'key3value' => '3450',

                ]
            ],
            'section3' => [
                'src' => './assets/images/blog-3.jpg',
                'address' => 'Belmont Gardens, Chicago3',
                'discription' => 'The Most Inspiring Interior Design Of 2021',
                'discription2' => 'Beautiful Huge 1 Family House In Heart Of Westbury. Newly Renovated With New Wood3',
                'date' => 'Apr 27, 2022',
                'price' => '$34,9003',
                'interior_url' => 'www.facebook.com',
                'features' => [
                    'key1' => 'Bedrooms',
                    'key2' => 'Bathrooms',
                    'key3' => 'Square Ft',
                    'key1value' => '4',
                    'key2value' => '2',
                    'key3value' => '3450',

                ]
            ],
        ];
        // return view('content/home/home');
        return view('common/body' , $data);
    }

    // Added by Raja Kumar [02-07-2025] Default page where contact will be shown
    public function contact(): string
    {   
        $data = [
            'content' => 'content/home/contact'
        ];
        // return view('content/home/home');
        return view('common/body' , $data);
    }

    // Added by Raja Kumar [02-07-2025] Fetch contact values
    public function contactfetch(): string
    {  
        print_r($this->request->getPost()); die;
        // Here you can process the data, e.g., save it to a database or send an email
    }
}
