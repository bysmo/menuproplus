<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QrShortLink extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'last_scanned_at' => 'datetime',
        'scan_count'      => 'integer',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Generate a unique, short, secure alphanumeric code (e.g. "t7Xk9A").
     */
    public static function generateUniqueCode(int $length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
