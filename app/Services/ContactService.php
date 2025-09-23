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

}
