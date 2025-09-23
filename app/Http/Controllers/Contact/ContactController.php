<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function __construct(protected ContactService $contactService)
    {
        // $this->authorizeResource(Contact::class, 'contact');
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
        $contacts = $this->contactService->getContacts(
            auth()->id(),
            $filters,
            $filters['per_page'] ?? 15
        );
        return Inertia::render('Contacts/Index', [
            'contacts' => ContactResource::collection($contacts),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Contacts/Create', [
            'groups' => auth()->user()->contactGroups()->select('id', 'name', 'color')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = auth()->user()
                    ->contacts()->create($request->validated());
        return response()->json([
            'message' => 'Contact created successfully', 
            'contact' => $contact
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return Inertia::render('Contacts/Show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        return Inertia::render('Contacts/Edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());
        return response()->json(['message' => 'Contact updated successfully', 'contact' => $contact], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(['message' => 'Contact deleted successfully'], 200);
    }
}
