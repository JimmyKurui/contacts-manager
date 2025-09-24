<?php

namespace App\Repositories;

use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Models\Group;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class GroupRepository implements GroupRepositoryInterface
{
    public function getAllGroupsForUser(int $userId): Collection
    {
        try {
            return Group::byUser($userId)->get();
        } catch (Exception $e) {
            Log::error('Failed to get groups for user ' . $userId . ': ' . $e->getMessage());
            throw $e;
        }
    }

    public function getGroupById(int $groupId): Group
    {
        try {
            return Group::findOrFail($groupId);
        } catch (Exception $e) {
            Log::error('Failed to get group by ID ' . $groupId . ': ' . $e->getMessage());
            throw $e;
        }
    }

    public function createGroup(array $data): Group
    {
        try {
            return Group::create($data);
        } catch (Exception $e) {
            Log::error('Failed to create group: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateGroup(int $groupId, array $data): Group
    {
        try {
            $group = Group::findOrFail($groupId);
            $group->update($data);
            return $group;
        } catch (Exception $e) {
            Log::error('Failed to update group ' . $groupId . ': ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteGroup(int $groupId): bool
    {
        try {
            $group = Group::findOrFail($groupId);
            $group->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete group ' . $groupId . ': ' . $e->getMessage());
            return false;
        }
    }

    public function attachContacts(int $groupId, array $contactIds, ?int $assignedBy = null): void
    {
        try {
            $group = Group::findOrFail($groupId);
            $pivotData = [];
            foreach ($contactIds as $contactId) {
                $pivotData[$contactId] = [
                    'assigned_at' => now(),
                    'assigned_by' => $assignedBy,
                ];
            }
            $group->contacts()->syncWithoutDetaching($pivotData);
        } catch (Exception $e) {
            Log::error('Failed to attach contacts to group ' . $groupId . ': ' . $e->getMessage());
        }
    }

    public function detachContacts(int $groupId, array $contactIds): void
    {
        try {
            $group = Group::findOrFail($groupId);
            $group->contacts()->detach($contactIds);
        } catch (Exception $e) {
            Log::error('Failed to detach contacts from group ' . $groupId . ': ' . $e->getMessage());
        }
    }
}