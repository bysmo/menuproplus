<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModifierGroupController extends Controller
{
    public function index()
    {
        abort_if(!in_array('Menu Item', restaurant_modules()), 403);
        abort_if((!user_can('Show Menu Item')), 403);
        return view('modifier_groups.index');
    }

    public function create()
    {
        abort_if((!user_can('Create Menu Item')), 403);
        return view('modifier_groups.create');
    }

    public function edit($id)
    {
        abort_if((!user_can('Update Menu Item')), 403);

        return view('modifier_groups.edit', compact('id'));
    }
}
