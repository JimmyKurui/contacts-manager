<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Pivot;

class ContactGroup extends Pivot
{
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ContactGroup::class, 'contact_group_id');
    }
}
