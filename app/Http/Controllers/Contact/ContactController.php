<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Group\GroupController;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Services\ContactService;
use App\Services\GroupService;
use App\Traits\ErrorResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    use ErrorResponseTrait;

    private const CACHE_TAG = 'contacts_user:';
    
    public function __construct(protected ContactService $contactService, protected GroupService $groupService)
    {
        // $this->authorizeResource(Contact::class, 'contact');
        $this->contactsCacheTag = self::CACHE_TAG . auth()->id();
    }

    public static function getContactsCacheTag(): string {
        return self::CACHE_TAG . auth()->id();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'sort' => 'nullable|in:name,company,created_at',
            'direction' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);
        $contactsCacheKey = $this->contactsCacheTag . ':filters:' . md5(json_encode($filters));
        $contacts = Cache::tags([$this->contactsCacheTag])->remember($contactsCacheKey, now()->addMinutes(5), function () use ($filters) {
            return $this->contactService->getContacts(
                auth()->id(),
                $filters,
                $filters['per_page'] ?? null
            );
        });

        $groupsCacheKey = GroupController::getGroupsCacheKey();
        $groups = Cache::remember($groupsCacheKey, now()->addMinutes(5), function () {
            return $this->groupService->getGroupsForUser(auth()->id());
        });

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
            'groups' => $groups->toResourceCollection(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $groups = $this->groupService->getGroupsForUser(auth()->id());
        return Inertia::render('Contacts/Create', [
            'groups' => $groups->toResourceCollection()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $data['user_id'] ??= auth()->id();
            $contact = $this->contactService->createContact($data);
            DB::commit();
            Cache::tags([$this->contactsCacheTag])->flush();
            return response()->json([
                'message' => 'Contact created successfully',
                'contact' => $contact->toResource()
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, 'Failed to create contact', 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return Inertia::render('Contacts/Show', [
            'contact' => $contact->toResource()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        return Inertia::render('Contacts/Edit', [
            'contact' => $contact->toResource()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        try {
            DB::beginTransaction();
            $updatedContact = $this->contactService->updateContact($contact->id, $request->validated());
            DB::commit();
            Cache::tags([$this->contactsCacheTag])->flush();
            return response()->json([
                'message' => 'Contact updated successfully', 
                'contact' => $updatedContact->toResource()
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, 'Failed to update contact', 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        try {
            DB::beginTransaction();
            $this->contactService->deleteContact($contact->id);
            DB::commit();
            Cache::tags([$this->contactsCacheTag])->flush();
            return response()->json(['message' => 'Contact deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, 'Failed to delete contact', 422);
        }
    }
}
