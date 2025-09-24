<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;


class ContactRepository implements ContactRepositoryInterface
{
    public function getAllContacts()
    {
        try {
            return Contact::with(['groups:id,name,color'])->withCount(['interactions']);
        } catch (Exception $e) {
            Log::error('Failed to get all contacts: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getFilteredContacts(int $userId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        try {
            $query = Contact::query()->byUser($userId)
                ->with(['groups:id,name,color'])
                ->withCount(['interactions']);
                
            $this->applyFilters($query, $filters);
            $this->applySorting($query, $filters);
            return $query->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Failed to get filtered contacts: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAllContactsForUser(int $userId): ?Collection
    {
        try {
            return Contact::byUser($userId)->with(['groups', 'interactions'])->get();
        } catch (Exception $e) {
            Log::error('Failed to get contacts for user ' . $userId . ': ' . $e->getMessage());
            throw $e;
        }
    }

    public function getContactById(int $contactId): Contact
    {
        try {
            return Contact::with(['groups', 'interactions'])->findOrFail($contactId);
        } catch (Exception $e) {
            Log::error('Failed to get contact by ID ' . $contactId . ': ' . $e->getMessage());
            throw $e;
        }
    }

    public function createContact(array $contactDetails): Contact
    {
        try {
            $contact = Contact::create($contactDetails);
            return $contact;
        } catch (Exception $e) {
            Log::error('Failed to create contact: ' . $e->getMessage());
            throw $e;
        }
    }


    public function updateContact(int $contactId, array $newDetails): Contact
    {
        try {
            $contact = Contact::findOrFail($contactId);
            $contact->update($newDetails);
            return $contact;
        } catch (Exception $e) {
            Log::error('Failed to update contact ' . $contactId . ': ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteContact(int $contactId): bool
    {
        try {
            $contact = Contact::findOrFail($contactId);
            return (bool) $contact->delete();
        } catch (Exception $e) {
            Log::error('Failed to delete contact ' . $contactId . ': ' . $e->getMessage());
            throw $e;
        }
    }

    public function attachGroups(int $contactId, array $groupIds): void
    {
        try {
            $contact = Contact::findOrFail($contactId);
            $pivotData = [];
            foreach ($groupIds as $groupId) {
                $pivotData[$groupId] = [
                    'assigned_at' => now(),
                    'assigned_by' => $assignedBy ?? $contact->user->id,
                ];
            }
            $contact->groups()->syncWithoutDetaching($pivotData);
        } catch (Exception $e) {
            Log::error('Failed to attach groups to contact ' . $contactId . ': ' . $e->getMessage());
            throw $e;
        }
    }

    public function detachGroups(int $contactId, array $groupIds): void
    {
        try {
            $contact = Contact::findOrFail($contactId);
            $contact->groups()->syncWithoutDetaching($groupIds);
        } catch (Exception $e) {
            Log::error('Failed to detach groups from contact ' . $contactId . ': ' . $e->getMessage());
            throw $e;
        }
    }

    // -----------------------------------------------------------------------
     private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['company'])) {
            $query->where('company', 'LIKE', "%{$filters['company']}%");
        }
    }

    private function applySorting(Builder $query, array $filters): void
    {
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';

        switch ($sortField) {
            case 'name':
                $query->orderBy('first_name', $sortDirection)
                      ->orderBy('last_name', $sortDirection);
                break;
            case 'company':
                $query->orderBy('company', $sortDirection)
                      ->orderBy('first_name', 'asc');
                break;
            case 'last_contacted_at':
                $query->orderBy('last_contacted_at', $sortDirection)
                      ->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy($sortField, $sortDirection);
                break;
        }
    }
}
