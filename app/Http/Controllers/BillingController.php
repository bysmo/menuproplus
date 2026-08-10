<?php

namespace App\Http\Controllers;

use App\Models\User;
use Razorpay\Api\Api;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Models\GlobalInvoice;
use App\Models\OfflinePlanChange;
use App\Models\GlobalSubscription;

class BillingController extends Controller
{

    public function __construct()
    {
        // Billing/invoice data is sensitive: only the Super Admin role may view it,
        // not any global-scope (restaurant_id = null) user created via the
        // superadmin user-management flow with a lesser role.
        abort_if(!user()->hasRole('Super Admin'), 403);
    }

    public function index()
    {
        return view('billing.index');
    }

    public function offlinePlanRequests()
    {
        return view('billing.offline-request');
    }

}
