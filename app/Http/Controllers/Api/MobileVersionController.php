<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MobileVersionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class MobileVersionController extends Controller
{
    protected $versionService;

    public function __construct(MobileVersionService $versionService)
    {
        $this->versionService = $versionService;
    }

    public function checkVersion(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:ios,android',
            'version' => 'required|string'
        ]);

        $platform = $request->platform;
        $currentVersion = $request->version;
        $versions = Config::get('mobile_versions.' . $platform);

        if (!$versions) {
            return response()->json([
                'error' => 'Invalid platform'
            ], 400);
        }

        $latestVersion = $versions['latest_version'];
        $minRequiredVersion = $versions['min_required_version'];
        $updateUrl = $versions['update_url'];

        // Compare versions using version_compare
        $needsUpdate = version_compare($currentVersion, $latestVersion, '<');
        $isBelowMinimum = version_compare($currentVersion, $minRequiredVersion, '<');

        return response()->json([
            'is_latest' => !$needsUpdate,
            'needs_update' => $needsUpdate,
            'is_below_minimum' => $isBelowMinimum,
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'min_required_version' => $minRequiredVersion,
            'update_url' => $needsUpdate ? $updateUrl : null
        ]);
    }

    public function updateVersion(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:ios,android',
            'latest_version' => 'required|string',
            'min_required_version' => 'required|string'
        ]);

        try {
            $updatedVersion = $this->versionService->updateVersion(
                $request->platform,
                $request->only(['latest_version', 'min_required_version'])
            );

            return response()->json([
                'message' => 'Version updated successfully',
                'data' => $updatedVersion
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update version'
            ], 500);
        }
    }
} 