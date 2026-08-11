<?php

namespace App\Models;

use App\Livewire\Menu\MenuItems as MenuMenuItems;
use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Model;
use App\Models\MenuItems;
use App\Traits\HasBranch;

class KotPlace extends Model
{
    use HasBranch;

    protected $guarded = [];

    protected $table = 'kot_places';

    public function getNameAttribute($value)
    {
        if (in_array(trim($value ?? ''), ['Default Kitchen', 'Default Kitchen Bon de cuisine'])) {
            return __('modules.kot.defaultKitchen');
        }
        return $value;
    }

    public function printerSetting()
    {
        return $this->belongsTo(Printer::class, 'printer_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function kots()
    {
        return $this->hasMany(Kot::class, 'kitchen_palace_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'restaurant_id');
    }

    public function menuItems()
    {
           return $this->hasMany(MenuItem::class, 'kot_place_id');
    }

}
