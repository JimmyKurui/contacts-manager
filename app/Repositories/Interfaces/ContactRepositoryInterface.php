<?php

namespace App\Repositories\Interfaces;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


interface ContactRepositoryInterface
{
    public function getFilteredContacts(int $userId, array $filters, int $perPage): LengthAwarePaginator;
    public function getContactById(int $contactId): Contact;
    public function createContact(array $data): Contact;
    public function updateContact(int $contactId, array $newDetails): Contact;
    public function deleteContact(int $contactId): bool;
    public function attachGroups(int $contactId, array $groupIds): void;
    public function detachGroups(int $contactId, array $groupIds): void;
}