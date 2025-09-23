<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'color',
        'is_default',
    ];

    protected $with = ['contacts', 'user'];

    // =================== RELATIONSHIPS ==============
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

    // ================== UTILS ====================
    public static function starterGroups(int $userId): array
    {
        $groups = config('starterContactGroups');
        return array_map(function ($group) use ($userId) {
            return array_merge($group, ['user_id' => $userId]);
        }, $groups);
    }

    // ========================= EVENTS =================
    protected static function booted(): void
    {
        // Only one default group per user
        static::saving(function (Group $group) {
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
