<?php

namespace App\Http\Controllers;

use App\Models\CustomMenu;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;

class LandingSiteController extends Controller
{

    public function index()
    {
        $settings = GlobalSetting::first();
        $customMenu = CustomMenu::all();
        return view('landing-sites.index', compact('settings', 'customMenu'));
    }

    public function showMenu()
    {
        $customMenu = CustomMenu::all();
        // dd($customMenu);
        return view('layouts.landing', compact('customMenu'));
    }

}
