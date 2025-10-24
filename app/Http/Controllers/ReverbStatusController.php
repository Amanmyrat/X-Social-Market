<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReverbStatusController extends Controller
{
    /**
     * Display the Reverb status dashboard
     */
    public function index()
    {
        $reverbConfig = config('reverb.servers.reverb');
        $broadcastConfig = config('broadcasting.connections.reverb');
        
        // Check if Reverb is actually running
        $isRunning = $this->checkReverbStatus(
            $reverbConfig['host'],
            $reverbConfig['port']
        );
        
        return view('reverb-status', [
            'server' => [
                'host' => $reverbConfig['host'],
                'port' => $reverbConfig['port'],
                'running' => $isRunning,
            ],
            'app' => [
                'id' => config('reverb.apps.apps.0.app_id'),
                'key' => config('reverb.apps.apps.0.key'),
                'host' => config('reverb.apps.apps.0.options.host'),
                'port' => config('reverb.apps.apps.0.options.port'),
                'scheme' => config('reverb.apps.apps.0.options.scheme'),
            ],
            'timestamp' => now()->toDateTimeString(),
            'statistics' => $this->getStatistics(),
        ]);
    }

    /**
     * API endpoint to check Reverb status
     */
    public function apiCheck()
    {
        try {
            $reverbConfig = config('reverb.servers.reverb');
            $isRunning = $this->checkReverbStatus(
                $reverbConfig['host'],
                $reverbConfig['port']
            );
            
            $stats = $this->getStatistics();
            
            return response()->json([
                'status' => $isRunning ? 'online' : 'offline',
                'server' => $reverbConfig['host'] . ':' . $reverbConfig['port'],
                'timestamp' => now()->toIso8601String(),
                'statistics' => $stats,
                'uptime' => $this->getUptime(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if Reverb server is actually running
     */
    private function checkReverbStatus($host, $port)
    {
        // Try to connect to the WebSocket server
        $connection = @fsockopen($host, $port, $errno, $errstr, 2);
        
        if ($connection) {
            fclose($connection);
            
            // Update last seen timestamp
            Cache::put('reverb_last_seen', now(), now()->addMinutes(5));
            Cache::put('reverb_first_seen', Cache::get('reverb_first_seen', now()), now()->addDays(365));
            
            return true;
        }
        
        return false;
    }

    /**
     * Get statistics from cache/database
     */
    private function getStatistics()
    {
        return [
            'connections' => $this->getConnectionCount(),
            'messages_sent' => $this->getMessageCount(),
            'api_messages' => $this->getApiMessageCount(),
        ];
    }

    /**
     * Get connection count (from cache or default)
     */
    private function getConnectionCount()
    {
        return Cache::get('reverb_connections', 0);
    }

    /**
     * Get message count (from cache or default)
     */
    private function getMessageCount()
    {
        return Cache::get('reverb_messages_sent', 0);
    }

    /**
     * Get API message count (from cache or default)
     */
    private function getApiMessageCount()
    {
        return Cache::get('reverb_api_messages', 0);
    }

    /**
     * Get server uptime
     */
    private function getUptime()
    {
        $firstSeen = Cache::get('reverb_first_seen');
        
        if (!$firstSeen) {
            return 'N/A';
        }
        
        $diff = $firstSeen->diff(now());
        
        $parts = [];
        if ($diff->d > 0) {
            $parts[] = $diff->d . 'd';
        }
        if ($diff->h > 0) {
            $parts[] = $diff->h . 'h';
        }
        if ($diff->i > 0) {
            $parts[] = $diff->i . 'm';
        }
        
        return implode(' ', $parts) ?: 'Just started';
    }
}

