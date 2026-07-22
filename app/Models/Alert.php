<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'node_id',
        'type',
        'message',
        'is_resolved',
    ];

    /**
     * Get the node that generated the alert.
     */
    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }
}
