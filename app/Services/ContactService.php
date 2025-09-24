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

    public function getContacts(int $userId, array $filters, $perPage = null): LengthAwarePaginator
    {
        $contacts = $this->contactRepository->getFilteredContacts($userId, $filters, $perPage);
        return $contacts;
    }

    public function createContact(array $data): Contact
    {
        $contact = $this->contactRepository->createContact($data);
        array_key_exists('group_ids', $data) && $data['group_ids'] && $this->contactRepository->attachGroups($contact->id, $data['group_ids']);
        return $contact;
    }

    public function updateContact(int $contactId, array $data): Contact
    {
        $contact = $this->contactRepository->updateContact($contactId, $data);
        $data['groups'] && $this->contactRepository->attachGroups($contact->id, collect($data['groups'])->pluck('id')->toArray());
        return $contact;
    }

    public function deleteContact(int $contactId): bool
    {
        return $this->contactRepository->deleteContact($contactId);
    }

}
