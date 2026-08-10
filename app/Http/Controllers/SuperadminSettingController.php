<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperadminSettingController extends Controller
{

    public function __construct()
    {
        // SaaS-wide settings and superadmin user/role management are
        // Super-Admin-only, not for any global-scope (restaurant_id = null) account.
        abort_if(!user()->hasRole('Super Admin'), 403);
    }

    public function index()
    {
        return view('superadmin-settings.index');
    }

    public function users()
    {
        return view('superadmin-settings.super-admin-list');
    }

}
