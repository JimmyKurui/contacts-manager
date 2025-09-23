<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Scout\Searchable;

class Contact extends Model
{
    use HasFactory, SoftDeletes;


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_group', 'contact_id', 'group_id');
    }

    public function isInGroup(ContactGroup $group): bool
    {
        return $this->groups()->where('contact_group.id', $group->id)->exists();
    }

    public function addToGroups(array $groupIds, ?int $assignedBy = null): void
    {
        $pivotData = [];
        foreach ($groupIds as $groupId) {
            $pivotData[$groupId] = [
                'assigned_at' => now(),
                'assigned_by' => $assignedBy ?? $this->user_id,
            ];
        }
        
        $this->groups()->syncWithoutDetaching($pivotData);
    }

    public function removeFromGroups(array $groupIds): void
    {
        $this->groups()->detach($groupIds);
    }


    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class)
            ->orderBy('ended_at', 'desc');
    }

    // ========================= UTILITIES ==============================
     public function toggleFavorite(): void
    {
        $this->update(['is_favorite' => !$this->is_favorite]);
    }

    // ========================= EVENTS ============================
    protected static function booted(): void
    {
        // Automatically assign to default group when creating a contact
        static::created(function (Contact $contact) {
            $defaultGroup = ContactGroup::where('user_id', $contact->user_id)
                ->where('is_default', true)
                ->first();

            if ($defaultGroup) {
                $contact->groups()->attach($defaultGroup->id, [
                    'assigned_at' => now(),
                    'assigned_by' => $contact->user_id,
                ]);
            }
        });

        // Clean up relationships when deleting
        static::deleting(function (Contact $contact) {
            if ($contact->isForceDeleting()) {
                $contact->interactions()->delete();
                $contact->groups()->detach();
            }
        });
    }
}