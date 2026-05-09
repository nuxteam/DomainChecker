<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    protected $fillable = [
        'user_id', 'url', 'name', 'method', 'timeout', 'auto_check', 'interval', 'notify_on_down',
    ];

    protected $casts = [
        'auto_check' => 'boolean',
        'notify_on_down' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(DomainCheck::class);
    }

    public function latestCheck()
    {
        return $this->hasOne(DomainCheck::class)->latestOfMany();
    }
}