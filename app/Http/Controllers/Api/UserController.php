<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\User\UserCheckContactsRequest;
use App\Http\Resources\ContactResource;
use App\Models\User;
use App\Services\UserService;
use App\Transformers\UserSimpleTransformer;
use App\Transformers\UserTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rules\Password;

class UserController extends ApiBaseController
{
    public function __construct(
        protected UserService $service
    ) {
        parent::__construct();
    }

    /**
     * Update the user password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $this->service->updatePassword($validated, Auth::user());

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Update the user phone.
     */
    public function updatePhone(Request $request): JsonResponse
    {
        $validated = $request->validate(['phone' => ['required', 'integer', 'unique:'.User::class]]);
        $this->service->updatePhone($validated, Auth::user());

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'password' => ['required', 'confirmed', Password::defaults()],
                'phone' => ['required', 'integer'],
            ]
        );

        $user = User::firstWhere('phone', $validated['phone']);

        $this->service->newPassword($validated, $user);

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Delete user.
     */
    public function delete(Request $request): JsonResponse
    {
        $request->user()->delete();

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Make account business
     */
    public function makeAccountBusiness(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $request->user()->update([
            'type' => User::TYPE_SELLER,
        ]);

        $request->user()->profile()->update($validated);

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Get all users list
     */
    public function getAll(): JsonResponse
    {
        return $this->respondWithPaginator(User::latest()->paginate(10), new UserTransformer());
    }

    /**
     * Search users
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['search_query' => ['required', 'string']]);
        $users = $this->service->search($request->all());

        return $this->respondWithCollection($users, new UserSimpleTransformer());
    }

    /**
     * Check user contacts
     */
    public function checkContacts(UserCheckContactsRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $results = $this->service->checkAndRetrieveContacts($validated['contacts'], Auth::user());

        return ContactResource::collection(collect($results));
    }

    /**
     * Check availability
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:phone,username,email',
            'value' => 'required|string',
        ]);

        $exists = User::where($validated['type'], $validated['value'])->exists();

        return new JsonResponse(['available' => ! $exists]);
    }
}
