<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name', 
        'email',
        'mobile_phone',
        'work_phone',
        'company',
        'job_title',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'date_of_birth',
        'notes',
        'is_favorite',
        'avatar_path',
    ];

    // ===================== RELATIONSHIPS ===================
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'contact_group', 'contact_id', 'group_id');
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

    // ========================= SCOPES ============================
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // ========================= EVENTS ============================
    protected static function booted(): void
    {
        // Automatically assign to default group when creating a contact
        static::created(function (Contact $contact) {
            $defaultGroup = Group::where('user_id', $contact->user_id)->where('is_default', true)->first();

            if ($defaultGroup) {
                $contact->groups()->attach($defaultGroup->id, [
                    'assigned_at' => now(),
                    'assigned_by' => $contact->user_id,
                ]);
            }
        });

        static::deleting(function (Contact $contact) {
            if ($contact->isForceDeleting()) {
                $contact->interactions()->delete();
                $contact->groups()->detach();
            }
        });
    }
}