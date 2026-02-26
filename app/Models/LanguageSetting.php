<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\BaseModel;

class LanguageSetting extends BaseModel
{
    protected $guarded = ['id'];

    const LANGUAGES_TRANS = [
        'en' => 'English',
        'fr' => 'Français'
    ];

    const LANGUAGES = [
        [
            'language_code' => 'en',
            'flag_code' => 'gb',
            'language_name' => 'English',
            'active' => 1,
            'is_rtl' => 0,
        ],
        [
            'language_code' => 'fr',
            'flag_code' => 'fr',
            'language_name' => 'Français',
            'active' => 0,
            'is_rtl' => 0,
        ],
    ];


    public function flagUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return asset('flags/1x1/' . strtolower($this->flag_code) . '.svg');
        });
    }
}
