<?php

namespace App\Repositories\Interfaces;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

interface GroupRepositoryInterface
{
    public function getAllGroupsForUser(int $userId): Collection;
    public function getGroupById(int $groupId): Group;
    public function createGroup(array $data): Group;
    public function updateGroup(int $groupId, array $data): Group;
    public function deleteGroup(int $groupId): bool;
    public function attachContacts(int $groupId, array $contactIds, ?int $assignedBy = null): void;
    public function detachContacts(int $groupId, array $contactIds): void;
}