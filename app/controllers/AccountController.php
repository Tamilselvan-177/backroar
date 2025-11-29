<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Order;

class AccountController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        // Check if user is logged in
        if (!\isLoggedIn()) {
            \flash('error', 'Please login to access your account');
            \redirect('/login');
            exit;
        }

        $this->userModel = new User();
    }

    /**
     * Show account dashboard
     */
    public function dashboard()
    {
        $user = $this->userModel->getUserById(\userId());

        $this->view('account/dashboard.twig', [
            'title' => 'My Account - BlackRoar',
            'user' => $user
        ]);
    }

    /**
     * Show profile page
     */
    public function profile()
    {
        $user = $this->userModel->getUserById(\userId());

        $this->view('account/profile.twig', [
            'title' => 'My Profile - BlackRoar',
            'user' => $user,
            'errors' => $_SESSION['errors'] ?? [],
            'old' => $_SESSION['old'] ?? []
        ]);

        unset($_SESSION['errors'], $_SESSION['old']);
    }

    /**
     * Update profile
     */
    public function updateProfile()
    {
        $name = \clean($_POST['name'] ?? '');
        $phone = \clean($_POST['phone'] ?? '');

        // Validation
        $validator = new \App\Helpers\Validator($_POST);
        $validator->required('name', 'Name is required')
                  ->min('name', 3, 'Name must be at least 3 characters')
                  ->required('phone', 'Phone number is required')
                  ->phone('phone', 'Invalid phone number');

        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['old'] = ['name' => $name, 'phone' => $phone];
            return $this->redirect('/account/profile');
        }

        // Update profile
        $updated = $this->userModel->updateProfile(\userId(), [
            'name' => $name,
            'phone' => $phone
        ]);

        if ($updated) {
            $_SESSION['user_name'] = $name;
            \flash('success', 'Profile updated successfully!');
        } else {
            \flash('error', 'Failed to update profile');
        }

        return $this->redirect('/account/profile');
    }

    /**
     * Show orders page
     */
    public function orders()
    {
        $orderModel = new Order();
        $page = (int)($_GET['page'] ?? 1);
        $result = $orderModel->getUserOrders(\userId(), $page, 10);

        $this->view('account/orders.twig', [
            'title' => 'My Orders - BlackRoar',
            'orders' => $result['orders'],
            'pagination' => [
                'total' => $result['total'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages']
            ]
        ]);
    }
}
