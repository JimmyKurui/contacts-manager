<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Services\GroupService;
use App\Http\Requests\Group\GroupStoreRequest;
use App\Http\Requests\Group\GroupUpdateRequest;
use App\Traits\ErrorResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Http\JsonResponse;

class GroupController extends Controller
{
    use ErrorResponseTrait;

    public function __construct(private GroupService $groupService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $groups = $this->groupService->getGroupsForUser($request->user()->id);
        return Inertia::render('Groups/Index', [
            'groups' => $groups->toResourceCollection(),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $group = $this->groupService->createGroup($data);
            DB::commit();
            return response()->json([
                'message' => 'Group created successfully',
                'group' => $group->toResource()
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, 'Failed to create group', 422);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $group = $this->groupService->getGroupById($id);
            return response()->json(['group' => $group->toResource()]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to fetch group');
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(GroupUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $group = $this->groupService->updateGroup($id, $request->validated());
            DB::commit();
            return response()->json([
                'message' => 'Group updated successfully',
                'group' => $group->toResource()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, 'Failed to update group', 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $deleted = $this->groupService->deleteGroup($id);
            DB::commit();
            return response()->json(['message' => 'Group deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, 'Failed to delete group', 422);
        }
    }

    public function attachContact(Request $request, $groupId): JsonResponse
    {
        try {
            $request->validate([
                'contactId' => 'required|integer|exists:contacts,id',
            ]);
            DB::beginTransaction();
            $this->groupService->attachContacts($groupId, [$request->contactId], auth()->id());
            DB::commit();
            return response()->json([
                'message' => 'Contact attached to group successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, 'Failed to attach contact to group', 422);
        }
    }

    public function detachContact(Request $request, $groupId): JsonResponse
    {
        try {
            $request->validate([
                'contactId' => 'required|integer|exists:contacts,id',
            ]);
            DB::beginTransaction();
            $this->groupService->detachContacts($groupId, [$request->contactId], auth()->id());
            DB::commit();
            return response()->json([
                'message' => 'Contact attached to group successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, 'Failed to detach contact from group', 422);
        }
    }
}
