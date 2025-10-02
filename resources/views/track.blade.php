<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Location Tracking - Sellora</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Sellora Tracker">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        #map {
            height: 300px;
            width: 100%;
            border-radius: 0.5rem;
        }
        
        .tracking-active {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        
        .status-active {
            background-color: #10b981;
        }
        
        .status-inactive {
            background-color: #ef4444;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-6 max-w-md">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Location Tracking</h1>
            <p class="text-gray-600">Track your location for field visits</p>
        </div>
        
        <!-- Status Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <span id="statusIndicator" class="status-indicator status-inactive"></span>
                    <span id="statusText" class="font-medium text-gray-700">Tracking Stopped</span>
                </div>
                <button id="toggleTracking" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Start Tracking
                </button>
            </div>
            
            <div id="locationInfo" class="text-sm text-gray-600 hidden">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="font-medium">Latitude:</span>
                        <span id="currentLat">-</span>
                    </div>
                    <div>
                        <span class="font-medium">Longitude:</span>
                        <span id="currentLng">-</span>
                    </div>
                    <div>
                        <span class="font-medium">Accuracy:</span>
                        <span id="currentAccuracy">-</span>m
                    </div>
                    <div>
                        <span class="font-medium">Last Update:</span>
                        <span id="lastUpdate">-</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Map Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Your Location</h2>
            <div id="map"></div>
            <p class="text-xs text-gray-500 mt-2">
                Map data © <a href="https://openstreetmap.org" target="_blank" class="text-blue-600">OpenStreetMap</a> contributors
            </p>
        </div>
        
        <!-- Settings Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Settings</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="updateInterval" class="block text-sm font-medium text-gray-700 mb-1">
                        Update Interval (seconds)
                    </label>
                    <select id="updateInterval" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="15">15 seconds</option>
                        <option value="30" selected>30 seconds</option>
                        <option value="60">60 seconds</option>
                    </select>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="highAccuracy" checked 
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="highAccuracy" class="ml-2 text-sm text-gray-700">
                        High accuracy GPS (uses more battery)
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="wakeLock" 
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="wakeLock" class="ml-2 text-sm text-gray-700">
                        Keep screen awake (if supported)
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Messages -->
        <div id="messages" class="mt-6"></div>
    </div>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
    
    <script>
        class LocationTracker {
            constructor() {
                this.isTracking = false;
                this.watchId = null;
                this.map = null;
                this.marker = null;
                this.accuracyCircle = null;
                this.wakeLock = null;
                this.lastUpdateTime = null;
                this.updateInterval = 30000; // 30 seconds default
                
                this.initMap();
                this.bindEvents();
                this.checkGeolocationSupport();
            }
            
            initMap() {
                // Initialize map centered on India
                this.map = L.map('map').setView([20.5937, 78.9629], 5);
                
                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(this.map);
            }
            
            bindEvents() {
                document.getElementById('toggleTracking').addEventListener('click', () => {
                    this.toggleTracking();
                });
                
                document.getElementById('updateInterval').addEventListener('change', (e) => {
                    this.updateInterval = parseInt(e.target.value) * 1000;
                });
                
                document.getElementById('wakeLock').addEventListener('change', (e) => {
                    if (e.target.checked) {
                        this.requestWakeLock();
                    } else {
                        this.releaseWakeLock();
                    }
                });
            }
            
            checkGeolocationSupport() {
                if (!navigator.geolocation) {
                    this.showMessage('Geolocation is not supported by this browser.', 'error');
                    document.getElementById('toggleTracking').disabled = true;
                }
            }
            
            async toggleTracking() {
                if (this.isTracking) {
                    this.stopTracking();
                } else {
                    await this.startTracking();
                }
            }
            
            async startTracking() {
                try {
                    // Request permission first
                    const permission = await navigator.permissions.query({name: 'geolocation'});
                    
                    if (permission.state === 'denied') {
                        this.showMessage('Location permission denied. Please enable location access.', 'error');
                        return;
                    }
                    
                    const options = {
                        enableHighAccuracy: document.getElementById('highAccuracy').checked,
                        timeout: 10000,
                        maximumAge: 0
                    };
                    
                    this.watchId = navigator.geolocation.watchPosition(
                        (position) => this.onLocationUpdate(position),
                        (error) => this.onLocationError(error),
                        options
                    );
                    
                    this.isTracking = true;
                    this.updateUI();
                    this.showMessage('Location tracking started', 'success');
                    
                    // Request wake lock if enabled
                    if (document.getElementById('wakeLock').checked) {
                        await this.requestWakeLock();
                    }
                    
                } catch (error) {
                    this.showMessage('Failed to start tracking: ' + error.message, 'error');
                }
            }
            
            stopTracking() {
                if (this.watchId) {
                    navigator.geolocation.clearWatch(this.watchId);
                    this.watchId = null;
                }
                
                this.isTracking = false;
                this.updateUI();
                this.showMessage('Location tracking stopped', 'info');
                this.releaseWakeLock();
            }
            
            async onLocationUpdate(position) {
                const { latitude, longitude, accuracy } = position.coords;
                const timestamp = new Date(position.timestamp);
                
                // Rate limiting check
                if (this.lastUpdateTime && 
                    (timestamp - this.lastUpdateTime) < this.updateInterval) {
                    return;
                }
                
                this.lastUpdateTime = timestamp;
                
                // Update UI
                this.updateLocationDisplay(latitude, longitude, accuracy, timestamp);
                this.updateMapPosition(latitude, longitude, accuracy);
                
                // Send to server
                await this.sendLocationToServer(latitude, longitude, accuracy);
            }
            
            onLocationError(error) {
                let message = 'Location error: ';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        message += 'Permission denied';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message += 'Position unavailable';
                        break;
                    case error.TIMEOUT:
                        message += 'Request timeout';
                        break;
                    default:
                        message += 'Unknown error';
                        break;
                }
                this.showMessage(message, 'error');
            }
            
            updateLocationDisplay(lat, lng, accuracy, timestamp) {
                document.getElementById('currentLat').textContent = lat.toFixed(6);
                document.getElementById('currentLng').textContent = lng.toFixed(6);
                document.getElementById('currentAccuracy').textContent = Math.round(accuracy);
                document.getElementById('lastUpdate').textContent = timestamp.toLocaleTimeString();
                document.getElementById('locationInfo').classList.remove('hidden');
            }
            
            updateMapPosition(lat, lng, accuracy) {
                // Remove existing marker and circle
                if (this.marker) {
                    this.map.removeLayer(this.marker);
                }
                if (this.accuracyCircle) {
                    this.map.removeLayer(this.accuracyCircle);
                }
                
                // Add new marker
                this.marker = L.marker([lat, lng]).addTo(this.map)
                    .bindPopup(`You are here<br>Accuracy: ${Math.round(accuracy)}m`);
                
                // Add accuracy circle
                this.accuracyCircle = L.circle([lat, lng], {
                    radius: accuracy,
                    color: '#3b82f6',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.1
                }).addTo(this.map);
                
                // Center map on location
                this.map.setView([lat, lng], 16);
            }
            
            async sendLocationToServer(latitude, longitude, accuracy) {
                try {
                    const response = await fetch('/api/locations', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Authorization': 'Bearer ' + this.getAuthToken()
                        },
                        body: JSON.stringify({
                            latitude,
                            longitude,
                            accuracy
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        if (response.status === 429) {
                            // Rate limited - this is expected, don't show error
                            return;
                        }
                        throw new Error(data.error || 'Failed to save location');
                    }
                    
                    // Success - could show a subtle indicator
                    console.log('Location saved successfully');
                    
                } catch (error) {
                    console.error('Failed to save location:', error);
                    this.showMessage('Failed to save location: ' + error.message, 'warning');
                }
            }
            
            getAuthToken() {
                // In a real app, this would get the token from localStorage or session
                // For now, return a placeholder
                return localStorage.getItem('auth_token') || 'placeholder-token';
            }
            
            updateUI() {
                const toggleBtn = document.getElementById('toggleTracking');
                const statusIndicator = document.getElementById('statusIndicator');
                const statusText = document.getElementById('statusText');
                
                if (this.isTracking) {
                    toggleBtn.textContent = 'Stop Tracking';
                    toggleBtn.className = 'bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors tracking-active';
                    statusIndicator.className = 'status-indicator status-active';
                    statusText.textContent = 'Tracking Active';
                } else {
                    toggleBtn.textContent = 'Start Tracking';
                    toggleBtn.className = 'bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors';
                    statusIndicator.className = 'status-indicator status-inactive';
                    statusText.textContent = 'Tracking Stopped';
                    document.getElementById('locationInfo').classList.add('hidden');
                }
            }
            
            async requestWakeLock() {
                try {
                    if ('wakeLock' in navigator) {
                        this.wakeLock = await navigator.wakeLock.request('screen');
                        this.showMessage('Screen wake lock activated', 'info');
                    }
                } catch (error) {
                    console.error('Wake lock failed:', error);
                }
            }
            
            releaseWakeLock() {
                if (this.wakeLock) {
                    this.wakeLock.release();
                    this.wakeLock = null;
                }
            }
            
            showMessage(message, type = 'info') {
                const messagesContainer = document.getElementById('messages');
                const messageDiv = document.createElement('div');
                
                const colors = {
                    success: 'bg-green-100 border-green-400 text-green-700',
                    error: 'bg-red-100 border-red-400 text-red-700',
                    warning: 'bg-yellow-100 border-yellow-400 text-yellow-700',
                    info: 'bg-blue-100 border-blue-400 text-blue-700'
                };
                
                messageDiv.className = `border-l-4 p-4 mb-4 rounded ${colors[type] || colors.info}`;
                messageDiv.textContent = message;
                
                messagesContainer.appendChild(messageDiv);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.parentNode.removeChild(messageDiv);
                    }
                }, 5000);
            }
        }
        
        // Initialize tracker when page loads
        document.addEventListener('DOMContentLoaded', () => {
            new LocationTracker();
        });
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('Page hidden - tracking continues in background (limited)');
            } else {
                console.log('Page visible - full tracking active');
            }
        });
    </script>
</body>
</html>