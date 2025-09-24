<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\ContactRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;


class ContactService
{
    public function __construct(private ContactRepository $contactRepository)
    {
    }

    public function getContacts(int $userId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $contacts = $this->contactRepository->getFilteredContacts($userId, $filters, $perPage);
        return $contacts;
    }

    public function createContact(array $data, int $userId): Contact
    {
        $data['user_id'] ??= $userId;
        $contact = $this->contactRepository->createContact($data);
        array_key_exists('group_ids', $data) && $data['group_ids'] && $this->contactRepository->attachGroups($contact->id, $data['group_ids']);
        return $contact->fresh();
    }

    public function updateContact(array $data): Contact
    {
        $contact = $this->contactRepository->updateContact($data['id'], $data);
        $data['group_ids'] && $this->contactRepository->attachGroups($contact->id, $data['group_ids']);
        return $contact->fresh();
    }

}
