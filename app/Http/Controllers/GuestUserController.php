<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestUserRequest;
use App\Http\Resources\GuestUserResource;
use App\Models\GuestUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GuestUserController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $sortField = $request->query('sort', 'created_on');
        $sortOrder = $request->query('order', 'desc');
        $itemsPerPage = $request->query('per_page', 10);

        // Normalize the items per page
        $normalizedItemsPerPage = max(1, min((int)$itemsPerPage, 100));

        // Mapping fields for sorting
        $sortFieldMapping = [
            'created_on' => 'created_at',
            'date_of_birth' => 'date_of_birth',
            'id' => 'id',
        ];

        // Resolve the actual sort field
        $sortField = $sortFieldMapping[$sortField] ?? 'created_at';

        // Fetch and sort GuestUser data
        $guestUsers = GuestUser::orderBy($sortField, $sortOrder)
            ->paginate($normalizedItemsPerPage);

        return GuestUserResource::collection($guestUsers);
    }



    public function store(StoreGuestUserRequest $request): JsonResponse
    {
        $guestUser = GuestUser::create($request->validated());

        return response()->json(new GuestUserResource($guestUser), 201);
    }


    public function show(GuestUser $guestUser): JsonResponse
    {
        return response()->json(new GuestUserResource($guestUser));
    }


    public function update(StoreGuestUserRequest $request, GuestUser $guestUser): JsonResponse
    {
        $guestUser->update($request->validated());
        return response()->json(new GuestUserResource($guestUser));
    }


    public function destroy(GuestUser $guestUser): JsonResponse
    {
        $guestUser->delete();
        return response()->json(['message' => 'User has been deleted successfully']);
    }
}
