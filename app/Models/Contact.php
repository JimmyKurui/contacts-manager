<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Traits\QueryParametersTrait;

class Contact extends Model
{
    use HasFactory, SoftDeletes;
    use QueryParametersTrait;

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

    protected $searchable = [
        'first_name',
        'last_name',
        'email',
        'company',
        'mobile_phone',
        'work_phone',
    ];

    protected $filterable = [
        'created_at',
        'date_of_birth',
        'is_favorite',
        'avatar_path',
        'city',
        'state',
        'country',
        'postal_code',
        'job_title'
    ];
    protected static $sortable = [
        'created_at',
        'date_of_birth',
        'first_name',
        'last_name',
        'is_favorite',
        'avatar_path',
        'city',
        'state',
        'country',
        'postal_code',
        'job_title'
    ];

    // ==================== ACCESSORS & MUTATORS ==================
    public static function getSortableFields(): array
    {
        return self::$sortable;
    }
    // ====================== END ACCESSORS & MUTATORS =================

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
    // ======================= END RELATIONSHIPS ===================

    // ========================= UTILITIES ==============================
    public function toggleFavorite(): void
    {
        $this->update(['is_favorite' => !$this->is_favorite]);
    }
    // ========================= END UTILITIES ==============================

    // ========================= SCOPES ============================
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
    // ========================= END SCOPES ============================

    // ========================= OBSERVERS ============================
    protected static function booted(): void
    {
        // Automatically assign to default group when creating a contact
        static::created(function (Contact $contact) {
            $defaultGroup = Group::byUser($contact->user_id)->where('is_default', true)->first();

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
    // ========================= END OBSERVERS ============================

}