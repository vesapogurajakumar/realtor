<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuth implements FilterInterface
{
    /**
     * Redirect to the admin login unless the session is authenticated.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to(base_url('public/admin/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
