<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactImport extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
