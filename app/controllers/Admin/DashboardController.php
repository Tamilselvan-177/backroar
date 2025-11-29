<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function __construct()
    {
        if (!\isAdmin()) {
            \flash('error', 'Admin access required');
            \redirect('/login');
            exit;
        }
    }

    public function index()
    {
        $this->view('admin/index.twig', [
            'title' => 'Admin Dashboard'
        ]);
    }
}

