<?php

declare(strict_types=1);

namespace App\Controllers;

use Config\Services;

class Errors extends BaseController
{
    /**
     * Branded 404 page (set as the 404 override).
     */
    public function show404(): string
    {
        $this->response->setStatusCode(404);

        return view('errors/branded_404', [
            'title'           => 'Page Not Found | Vesta Real Estate',
            'metaDescription' => 'The page you were looking for could not be found.',
            'featured'        => Services::property()->featured(3),
        ]);
    }
}
