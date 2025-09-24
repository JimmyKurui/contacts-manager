<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GroupService;
use App\Http\Requests\Group\GroupStoreRequest;
use App\Http\Requests\Group\GroupUpdateRequest;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\JsonResponse;

class GroupController extends Controller
{
    public function __construct(private GroupService $groupService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    $groups = $this->groupService->getGroupsForUser($request->user()->id);
        return response()->json([
            'groups' => $groups->toResourceCollection(),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Show group creation form']);
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
            return response()->json([
                'message' => 'Failed to create group',
                'error' => app()->environment('production') ? 'Server error' : $e->getMessage()
            ], 422);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $group = $this->groupService->getGroupById($id);
        if ($group) {
            return response()->json(['group' => $group->toResource()]);
        }
        return response()->json(['message' => 'Group not found'], 404);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $group = $this->groupService->getGroupById($id);
        if ($group) {
            return response()->json(['group' => $group->toResource()]);
        }
        return response()->json(['message' => 'Group not found'], 404);
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
            if ($group) {
                return response()->json([
                    'message' => 'Group updated successfully',
                    'group' => $group->toResource()
                ]);
            }
            return response()->json(['message' => 'Failed to update group'], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update group',
                'error' => app()->environment('production') ? 'Server error' : $e->getMessage()
            ], 422);
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
            if ($deleted) {
                return response()->json(['message' => 'Group deleted successfully']);
            }
            return response()->json(['message' => 'Failed to delete group'], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete group',
                'error' => app()->environment('production') ? 'Server error' : $e->getMessage()
            ], 422);
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
            return response()->json([
                'message' => 'Failed to attach contact to group',
                'error' => app()->environment('production') ? 'Server error' : $e->getMessage()
            ], 422);
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
            return response()->json([
                'message' => 'Failed to attach contact to group',
                'error' => app()->environment('production') ? 'Server error' : $e->getMessage()
            ], 422);
        }
    }
}
