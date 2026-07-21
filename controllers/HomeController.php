<?php
class HomeController
{
    public function index()
    {
        if (auth()->check()) {
            if (auth()->hasRole('member')) {
                redirect('portal/dashboard');
            }
            redirect('admin/dashboard');
        }
        redirect('auth/login');
    }
}