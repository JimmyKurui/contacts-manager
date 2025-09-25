<?php

namespace App\Services;

use App\Models\Group;
use App\Repositories\GroupRepository;
use Illuminate\Database\Eloquent\Collection;

class GroupService
{
    public function __construct(private GroupRepository $groupRepository)
    {
    }

    public function getGroupsForUser(int $userId): Collection
    {
        return $this->groupRepository->getAllGroupsForUser($userId);
    }

    public function getGroupById(int $groupId): Group
    {
        return $this->groupRepository->getGroupById($groupId);
    }

    public function createGroup(array $data): Group
    {
        return $this->groupRepository->createGroup($data)?->fresh();
    }

    public function updateGroup(int $groupId, array $data): Group
    {
        return $this->groupRepository->updateGroup($groupId, $data)?->fresh();
    }

    public function deleteGroup(int $groupId): bool
    {
        return $this->groupRepository->deleteGroup($groupId);
    }

    public function attachContacts(int $groupId, array $contactIds, ?int $assignedBy = null): void
    {
        $this->groupRepository->attachContacts($groupId, $contactIds, $assignedBy);
    }

    public function detachContacts(int $groupId, array $contactIds): void
    {
        $this->groupRepository->detachContacts($groupId, $contactIds);
    }
}
