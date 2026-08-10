<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        // Only the restaurant admin may change the tenant's subscription or
        // submit billing/payment requests, not any authenticated staff member.
        abort_if(!user()->hasRole('Admin_' . user()->restaurant_id), 403);

        return view('plans.index');
    }
}