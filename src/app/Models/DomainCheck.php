<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainCheck extends Model
{
    protected $fillable = [
        'domain_id', 'is_up', 'status_code', 'response_time', 'error',
    ];

    protected $casts = ['is_up' => 'boolean'];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}