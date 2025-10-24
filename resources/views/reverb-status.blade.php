<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reverb Status - WebSocket Server</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #1a202c;
            color: #e2e8f0;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: linear-gradient(135deg, {{ $server['running'] ? '#667eea 0%, #764ba2' : '#dc2626 0%, #991b1b' }} 100%);
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            background: {{ $server['running'] ? '#10b981' : '#ef4444' }};
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            animation: pulse 2s infinite;
        }
        
        .status-badge.offline {
            background: #ef4444;
            animation: pulse-fast 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        @keyframes pulse-fast {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 1px solid #4a5568;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 8px;
            font-family: 'Monaco', 'Courier New', monospace;
        }
        
        .stat-label {
            font-size: 12px;
            color: #a0aec0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: #2d3748;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 1px solid #4a5568;
        }
        
        .card h2 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #a0aec0;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #4a5568;
            align-items: center;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #a0aec0;
            font-weight: 500;
        }
        
        .info-value {
            color: #e2e8f0;
            font-family: 'Monaco', 'Courier New', monospace;
            background: #1a202c;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 13px;
        }
        
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 10px #10b981;
            animation: glow 2s infinite;
        }
        
        .status-dot.offline {
            background: #ef4444;
            box-shadow: 0 0 10px #ef4444;
        }
        
        @keyframes glow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .test-section {
            background: #2d3748;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 1px solid #4a5568;
        }
        
        .test-section h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .test-button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .test-button:hover:not(:disabled) {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .test-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .test-button.secondary {
            background: #4a5568;
        }
        
        .test-button.secondary:hover:not(:disabled) {
            background: #3d4a5c;
        }
        
        .test-result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 6px;
            display: none;
        }
        
        .test-result.success {
            background: #065f46;
            border: 2px solid #10b981;
            display: block;
        }
        
        .test-result.error {
            background: #7f1d1d;
            border: 2px solid #ef4444;
            display: block;
        }
        
        .connection-log {
            background: #1a202c;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            max-height: 250px;
            overflow-y: auto;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 12px;
            border: 1px solid #4a5568;
        }
        
        .log-entry {
            padding: 5px 0;
            color: #a0aec0;
        }
        
        .log-entry.success {
            color: #10b981;
        }
        
        .log-entry.error {
            color: #ef4444;
        }

        .log-entry.warning {
            color: #f59e0b;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #a0aec0;
        }
        
        .footer a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer a:hover {
            color: #5a67d8;
        }
        
        .code-block {
            background: #1a202c;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            overflow-x: auto;
            border: 1px solid #4a5568;
        }
        
        .code-block code {
            color: #10b981;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 13px;
        }
        
        .refresh-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #a0aec0;
            margin-top: 10px;
        }
        
        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid #4a5568;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert.error {
            background: #7f1d1d;
            border: 2px solid #ef4444;
            color: #fecaca;
        }

        .alert.warning {
            background: #78350f;
            border: 2px solid #f59e0b;
            color: #fde68a;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 24px;
            }
            
            .cards {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                🚀 Laravel Reverb
                <span class="status-badge {{ $server['running'] ? '' : 'offline' }}" id="status-badge">
                    ● {{ $server['running'] ? 'ONLINE' : 'OFFLINE' }}
                </span>
            </h1>
            <p style="margin-top: 10px; opacity: 0.9;">WebSocket Server Status Dashboard</p>
            <div class="refresh-indicator">
                <div class="spinner"></div>
                <span>Auto-refreshing every 10 seconds</span>
            </div>
        </div>

        @if(!$server['running'])
        <div class="alert error">
            <span style="font-size: 24px;">⚠️</span>
            <div>
                <strong>Reverb Server is Offline!</strong><br>
                <small>The WebSocket server is not responding. Please start it using: <code style="background: #4a5568; padding: 2px 6px; border-radius: 3px;">php artisan reverb:start</code></small>
            </div>
        </div>
        @endif

        <!-- Real-time Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value" id="stat-connections">{{ $statistics['connections'] ?? 0 }}</div>
                <div class="stat-label">Active Connections</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-messages">{{ $statistics['messages_sent'] ?? 0 }}</div>
                <div class="stat-label">Messages Sent</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-api-messages">{{ $statistics['api_messages'] ?? 0 }}</div>
                <div class="stat-label">API Messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-uptime">--</div>
                <div class="stat-label">Server Uptime</div>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <h2>📡 Server Configuration</h2>
                <div class="info-row">
                    <span class="info-label">Host</span>
                    <span class="info-value">{{ $server['host'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Port</span>
                    <span class="info-value">{{ $server['port'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value status-indicator">
                        <span class="status-dot {{ $server['running'] ? '' : 'offline' }}" id="status-dot"></span>
                        <span id="status-text">{{ $server['running'] ? 'Running' : 'Offline' }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Last Check</span>
                    <span class="info-value" id="last-check">{{ $timestamp }}</span>
                </div>
            </div>

            <div class="card">
                <h2>🔑 Application Config</h2>
                <div class="info-row">
                    <span class="info-label">App ID</span>
                    <span class="info-value">{{ $app['id'] ?? 'Not Set' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">App Key</span>
                    <span class="info-value">{{ substr($app['key'] ?? 'Not Set', 0, 20) }}...</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Host</span>
                    <span class="info-value">{{ $app['host'] ?? 'localhost' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Port</span>
                    <span class="info-value">{{ $app['port'] ?? '6001' }}</span>
                </div>
            </div>

            <div class="card">
                <h2>📊 System Information</h2>
                <div class="info-row">
                    <span class="info-label">Protocol</span>
                    <span class="info-value">Pusher Compatible</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Laravel Version</span>
                    <span class="info-value">{{ app()->version() }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">PHP Version</span>
                    <span class="info-value">{{ PHP_VERSION }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Environment</span>
                    <span class="info-value">{{ app()->environment() }}</span>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h2>🧪 Test WebSocket Connection</h2>
            <p style="margin-bottom: 20px; color: #a0aec0;">
                Click the button below to test if your frontend can connect to Reverb on port {{ $app['port'] ?? '6001' }}
            </p>
            
            <div class="button-group">
                <button class="test-button" onclick="testConnection()" id="test-btn">
                    Test Connection
                </button>
                <button class="test-button secondary" onclick="clearLog()">
                    Clear Log
                </button>
                <button class="test-button secondary" onclick="manualRefresh()">
                    Refresh Status
                </button>
            </div>
            
            <div id="test-result" class="test-result"></div>
            
            <div class="connection-log" id="connection-log">
                <div class="log-entry">Ready to test connection...</div>
            </div>

            <div class="code-block">
                <code>
// Frontend connection example:<br>
const echo = new Echo({<br>
&nbsp;&nbsp;broadcaster: 'pusher',<br>
&nbsp;&nbsp;key: '{{ $app['key'] ?? 'your-key' }}',<br>
&nbsp;&nbsp;wsHost: '{{ $app['host'] ?? 'localhost' }}',<br>
&nbsp;&nbsp;wsPort: {{ $app['port'] ?? '6001' }},<br>
&nbsp;&nbsp;forceTLS: false,<br>
&nbsp;&nbsp;disableStats: true,<br>
});
                </code>
            </div>
        </div>

        <!-- Advanced Tester Promotion -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); color: white; margin-bottom: 30px;">
            <h2 style="font-size: 20px; margin-bottom: 15px; color: white;">🔧 Need Advanced Debugging?</h2>
            <p style="margin-bottom: 15px; opacity: 0.95; line-height: 1.6;">
                The <strong>WebSocket Tester</strong> provides powerful debugging capabilities beyond the basic connection test:
            </p>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 20px;">
                <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 6px;">
                    <div style="font-size: 24px; margin-bottom: 8px;">📡</div>
                    <strong>Subscribe to Any Channel</strong>
                    <p style="font-size: 13px; opacity: 0.9; margin-top: 5px;">Public, private, and presence channels</p>
                </div>
                <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 6px;">
                    <div style="font-size: 24px; margin-bottom: 8px;">📨</div>
                    <strong>Listen for Events</strong>
                    <p style="font-size: 13px; opacity: 0.9; margin-top: 5px;">Monitor all events with detailed logging</p>
                </div>
                <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 6px;">
                    <div style="font-size: 24px; margin-bottom: 8px;">📤</div>
                    <strong>Send Client Events</strong>
                    <p style="font-size: 13px; opacity: 0.9; margin-top: 5px;">Test two-way communication</p>
                </div>
                <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 6px;">
                    <div style="font-size: 24px; margin-bottom: 8px;">🐛</div>
                    <strong>Debug Authentication</strong>
                    <p style="font-size: 13px; opacity: 0.9; margin-top: 5px;">Test private channel authorization</p>
                </div>
            </div>
            <a href="/websocket-tester.html" target="_blank" style="display: inline-block; background: white; color: #667eea; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.2)'">
                Open WebSocket Tester →
            </a>
        </div>

        <div class="footer">
            <p>
                Laravel Reverb WebSocket Server | 
                <a href="/telescope" target="_blank">View Telescope</a> | 
                <a href="/pulse" target="_blank">View Pulse</a> |
                <a href="/websocket-tester.html" target="_blank">WebSocket Tester</a>
            </p>
            <p style="margin-top: 10px; font-size: 14px;">Upgraded from beyondcode/laravel-websockets</p>
        </div>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        let autoRefreshInterval;
        let pusherConnection = null;

        function addLog(message, type = 'info') {
            const log = document.getElementById('connection-log');
            const entry = document.createElement('div');
            entry.className = `log-entry ${type}`;
            entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            log.appendChild(entry);
            log.scrollTop = log.scrollHeight;
        }

        function clearLog() {
            document.getElementById('connection-log').innerHTML = '<div class="log-entry">Log cleared...</div>';
            document.getElementById('test-result').style.display = 'none';
        }

        function updateUI(data) {
            // Update status badge
            const statusBadge = document.getElementById('status-badge');
            const statusDot = document.getElementById('status-dot');
            const statusText = document.getElementById('status-text');
            
            if (data.status === 'online') {
                statusBadge.textContent = '● ONLINE';
                statusBadge.className = 'status-badge';
                statusDot.className = 'status-dot';
                statusText.textContent = 'Running';
            } else {
                statusBadge.textContent = '● OFFLINE';
                statusBadge.className = 'status-badge offline';
                statusDot.className = 'status-dot offline';
                statusText.textContent = 'Offline';
            }

            // Update statistics
            if (data.statistics) {
                document.getElementById('stat-connections').textContent = data.statistics.connections || 0;
                document.getElementById('stat-messages').textContent = data.statistics.messages_sent || 0;
                document.getElementById('stat-api-messages').textContent = data.statistics.api_messages || 0;
            }

            // Update uptime
            if (data.uptime) {
                document.getElementById('stat-uptime').textContent = data.uptime;
            }

            // Update timestamp
            document.getElementById('last-check').textContent = new Date().toLocaleString();
        }

        function fetchStatus() {
            fetch('/api/reverb-check')
                .then(response => response.json())
                .then(data => {
                    updateUI(data);
                })
                .catch(error => {
                    console.error('Error fetching status:', error);
                });
        }

        function manualRefresh() {
            addLog('🔄 Manually refreshing status...', 'info');
            fetchStatus();
            addLog('✓ Status refreshed', 'success');
        }

        function testConnection() {
            const resultDiv = document.getElementById('test-result');
            const testBtn = document.getElementById('test-btn');
            resultDiv.style.display = 'none';
            testBtn.disabled = true;
            
            addLog('🔄 Attempting to connect to Reverb...', 'info');
            
            // Disconnect existing connection if any
            if (pusherConnection) {
                pusherConnection.disconnect();
            }
            
            try {
                pusherConnection = new Pusher('{{ $app['key'] ?? 'test-key' }}', {
                    wsHost: '{{ $app['host'] ?? 'localhost' }}',
                    wsPort: {{ $app['port'] ?? '6001' }},
                    forceTLS: false,
                    disableStats: true,
                    enabledTransports: ['ws', 'wss'],
                });

                pusherConnection.connection.bind('connecting', () => {
                    addLog('🔌 Connecting to WebSocket server...', 'info');
                });

                pusherConnection.connection.bind('connected', () => {
                    addLog('✅ Successfully connected to Reverb!', 'success');
                    addLog('📊 Connection ID: ' + pusherConnection.connection.socket_id, 'success');
                    resultDiv.className = 'test-result success';
                    resultDiv.textContent = '✓ Connection Successful! Reverb is working correctly.';
                    testBtn.disabled = false;
                    
                    // Fetch updated stats
                    setTimeout(() => {
                        fetchStatus();
                    }, 1000);
                    
                    // Disconnect after successful test
                    setTimeout(() => {
                        if (pusherConnection) {
                            pusherConnection.disconnect();
                            addLog('👋 Disconnected from server', 'info');
                        }
                    }, 3000);
                });

                pusherConnection.connection.bind('unavailable', () => {
                    addLog('❌ Connection unavailable - Reverb may not be running', 'error');
                    resultDiv.className = 'test-result error';
                    resultDiv.textContent = '✗ Connection Failed! Make sure Reverb is running on port {{ $app['port'] ?? '6001' }}';
                    testBtn.disabled = false;
                });

                pusherConnection.connection.bind('failed', () => {
                    addLog('❌ Connection failed', 'error');
                    resultDiv.className = 'test-result error';
                    resultDiv.textContent = '✗ Connection Failed! Check if Reverb server is running.';
                    testBtn.disabled = false;
                });

                pusherConnection.connection.bind('disconnected', () => {
                    addLog('🔌 Disconnected from server', 'info');
                });

                pusherConnection.connection.bind('error', (err) => {
                    addLog('❌ Connection error: ' + err.error?.data?.message || 'Unknown error', 'error');
                    testBtn.disabled = false;
                });

                // Timeout after 10 seconds
                setTimeout(() => {
                    if (pusherConnection && pusherConnection.connection.state === 'connecting') {
                        addLog('⏱️ Connection timeout - server may be offline', 'warning');
                        resultDiv.className = 'test-result error';
                        resultDiv.textContent = '✗ Connection Timeout! Server is not responding.';
                        pusherConnection.disconnect();
                        testBtn.disabled = false;
                    }
                }, 10000);

            } catch (error) {
                addLog(`❌ Error: ${error.message}`, 'error');
                resultDiv.className = 'test-result error';
                resultDiv.textContent = `✗ Error: ${error.message}`;
                testBtn.disabled = false;
            }
        }

        // Initial status fetch
        setTimeout(() => {
            fetchStatus();
        }, 1000);

        // Auto-refresh status every 10 seconds
        autoRefreshInterval = setInterval(() => {
            fetchStatus();
        }, 10000);

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (pusherConnection) {
                pusherConnection.disconnect();
            }
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        });
    </script>
</body>
</html>
