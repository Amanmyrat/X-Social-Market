<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class MobileVersionService
{
    protected $envPath;

    public function __construct()
    {
        $this->envPath = base_path('.env');
    }

    public function updateVersion(string $platform, array $data)
    {
        if (!in_array($platform, ['ios', 'android'])) {
            throw new \InvalidArgumentException('Invalid platform');
        }

        // Validate version format
        if (!$this->isValidVersion($data['latest_version']) || !$this->isValidVersion($data['min_required_version'])) {
            throw new \InvalidArgumentException('Invalid version format');
        }

        $prefix = strtoupper($platform);
        $updates = [
            "{$prefix}_LATEST_VERSION" => $data['latest_version'],
            "{$prefix}_MIN_REQUIRED_VERSION" => $data['min_required_version']
        ];

        $this->updateEnvFile($updates);

        // Get the current update URL from config
        $updateUrl = Config::get("mobile_versions.{$platform}.update_url");

        return [
            'latest_version' => $data['latest_version'],
            'min_required_version' => $data['min_required_version'],
            'update_url' => $updateUrl
        ];
    }

    protected function isValidVersion(string $version): bool
    {
        return (bool) preg_match('/^\d+\.\d+\.\d+$/', $version);
    }

    protected function updateEnvFile(array $updates)
    {
        $envContent = File::get($this->envPath);
        $lines = explode("\n", $envContent);

        foreach ($updates as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        File::put($this->envPath, $envContent);
    }
} 