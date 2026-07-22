<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Telemetry extends Model
{
    use HasFactory;

    protected $fillable = [
        'node_id',
        'temperature',
        'humidity',
        'pressure',
        'roll',
        'pitch',
        'latitude',
        'longitude',
        'water_level',
        'weather_condition',
    ];

    /**
     * Get the node that owns the telemetry.
     */
    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }
}
