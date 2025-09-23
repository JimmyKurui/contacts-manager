<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_group', 'cgroup_id', 'contact_id')
            ->withPivot(['assigned_at', 'assigned_by'])
            ->withTimestamps()
            ->using(ContactGroup::class);
    }

    public function hasContact(Contact $contact): bool
    {
        return $this->contacts()->where('contacts.id', $contact->id)->exists();
    }

    public function addContacts(array $contactIds, ?int $assignedBy = null): void
    {
        $pivotData = [];
        foreach ($contactIds as $contactId) {
            $pivotData[$contactId] = [
                'assigned_at' => now(),
                'assigned_by' => $assignedBy ?? $this->user_id,
            ];
        }
        
        $this->contacts()->syncWithoutDetaching($pivotData);
    }

    public function removeContacts(array $contactIds): void
    {
        $this->contacts()->detach($contactIds);
    }

    // ========================= EVENTS =================
    protected static function booted(): void
    {
        // Only one default group per user
        static::saving(function (ContactGroup $group) {
            if ($group->is_default) {
                static::where('user_id', $group->user_id)
                    ->where('id', '!=', $group->id)
                    ->update(['is_default' => false]);
            }
        });

        // No deletion of default groups that have contacts
        static::deleting(function (ContactGroup $group) {
            if ($group->is_default && $group->contacts()->count() > 0) {
                throw new \Exception('Cannot delete default group that contains contacts.');
            }
        });
    }
}
