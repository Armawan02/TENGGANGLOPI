<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Node extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mac_address',
        'status',
    ];

    /**
     * Get all of the telemetries for the Node
     */
    public function telemetries(): HasMany
    {
        return $this->hasMany(Telemetry::class);
    }

    /**
     * Get all of the alerts for the Node
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }
}
