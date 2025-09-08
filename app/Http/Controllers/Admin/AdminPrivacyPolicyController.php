<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrivacyPolicyRequest;
use App\Http\Resources\PrivacyPolicyResource;
use App\Models\PrivacyPolicy;
use Illuminate\Http\JsonResponse;

class AdminPrivacyPolicyController extends Controller
{
    /**
     * Get privacy
     *
     */
    public function getPrivacy(): PrivacyPolicyResource|JsonResponse
    {
        $privacy = PrivacyPolicy::first();
        if (!$privacy) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'No privacy policy found.'
            ], 404);
        }
        return new PrivacyPolicyResource($privacy);
    }

    /**
     * Create or update privacy
     *
     */
    public function storeOrUpdate(PrivacyPolicyRequest $request): PrivacyPolicyResource
    {
        $privacy = PrivacyPolicy::firstOrNew([]);
        $privacy->content_en = $request->content_en;
        $privacy->content_ru = $request->content_ru;
        $privacy->content_tk = $request->content_tk;
        $privacy->save();

        return new PrivacyPolicyResource($privacy);
    }

    /**
     * Delete privacy
     *
     */
    public function deletePrivacy(): JsonResponse
    {
        $privacy = PrivacyPolicy::first();
        if ($privacy) {
            $privacy->delete();
            return new JsonResponse(['message' => 'Privacy policy deleted successfully!']);
        }

        return new JsonResponse(['message' => 'No privacy policy found!'], 404);
    }
}
