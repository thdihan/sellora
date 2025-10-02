<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Team Map - Sellora</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Sellora Team Map">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        #map {
            height: calc(100vh - 120px);
            width: 100%;
        }
        
        .sidebar {
            height: calc(100vh - 120px);
            overflow-y: auto;
        }
        
        .user-item {
            transition: all 0.2s ease;
        }
        
        .user-item:hover {
            background-color: #f3f4f6;
        }
        
        .user-item.selected {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        
        .status-online {
            background-color: #10b981;
        }
        
        .status-offline {
            background-color: #ef4444;
        }
        
        .status-away {
            background-color: #f59e0b;
        }
        
        .role-badge {
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .role-mr {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .role-asm {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .role-zsm {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .role-nsm {
            background-color: #fce7f3;
            color: #be185d;
        }
        
        .role-admin {
            background-color: #f3e8ff;
            color: #7c3aed;
        }
        
        .refresh-indicator {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex flex-col h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Team Map</h1>
                    <p class="text-gray-600">Real-time team location tracking</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <span id="refreshIndicator" class="w-4 h-4 mr-2">🔄</span>
                        <span>Last updated: <span id="lastUpdate">-</span></span>
                    </div>
                    <button id="refreshBtn" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Refresh Now
                    </button>
                </div>
            </div>
        </div>
        
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
            <div class="w-80 bg-white border-r sidebar">
                <!-- Filters -->
                <div class="p-4 border-b">
                    <div class="space-y-3">
                        <div>
                            <label for="searchFilter" class="block text-sm font-medium text-gray-700 mb-1">
                                Search Team Members
                            </label>
                            <input type="text" id="searchFilter" placeholder="Search by name..." 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        </div>
                        
                        <div>
                            <label for="roleFilter" class="block text-sm font-medium text-gray-700 mb-1">
                                Filter by Role
                            </label>
                            <select id="roleFilter" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">All Roles</option>
                                <option value="MR">MR</option>
                                <option value="ASM">ASM</option>
                                <option value="ZSM">ZSM</option>
                                <option value="NSM">NSM</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">
                                Filter by Status
                            </label>
                            <select id="statusFilter" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">All Status</option>
                                <option value="online">Online (< 10 min)</option>
                                <option value="away">Away (10-60 min)</option>
                                <option value="offline">Offline (> 60 min)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Team List -->
                <div id="teamList" class="p-4">
                    <div class="text-center text-gray-500 py-8">
                        <div class="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto mb-4"></div>
                        Loading team members...
                    </div>
                </div>
            </div>
            
            <!-- Map -->
            <div class="flex-1 relative">
                <div id="map"></div>
                
                <!-- Map Controls -->
                <div class="absolute top-4 right-4 bg-white rounded-lg shadow-md p-3 space-y-2">
                    <button id="centerMapBtn" 
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition-colors">
                        Center on Team
                    </button>
                    <button id="toggleClustering" 
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm transition-colors">
                        Toggle Clustering
                    </button>
                </div>
                
                <!-- Legend -->
                <div class="absolute bottom-4 left-4 bg-white rounded-lg shadow-md p-3">
                    <h3 class="font-medium text-gray-800 mb-2">Status Legend</h3>
                    <div class="space-y-1 text-sm">
                        <div class="flex items-center">
                            <span class="status-dot status-online"></span>
                            <span>Online (< 10 min)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="status-dot status-away"></span>
                            <span>Away (10-60 min)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="status-dot status-offline"></span>
                            <span>Offline (> 60 min)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
    
    <script>
        class TeamMap {
            constructor() {
                this.map = null;
                this.markers = new Map();
                this.teamData = [];
                this.filteredData = [];
                this.selectedUserId = null;
                this.refreshInterval = null;
                this.clusteringEnabled = false;
                
                this.initMap();
                this.bindEvents();
                this.startAutoRefresh();
                this.loadTeamData();
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
                document.getElementById('refreshBtn').addEventListener('click', () => {
                    this.loadTeamData();
                });
                
                document.getElementById('centerMapBtn').addEventListener('click', () => {
                    this.centerMapOnTeam();
                });
                
                document.getElementById('toggleClustering').addEventListener('click', () => {
                    this.toggleClustering();
                });
                
                document.getElementById('searchFilter').addEventListener('input', (e) => {
                    this.filterTeamData();
                });
                
                document.getElementById('roleFilter').addEventListener('change', (e) => {
                    this.filterTeamData();
                });
                
                document.getElementById('statusFilter').addEventListener('change', (e) => {
                    this.filterTeamData();
                });
            }
            
            async loadTeamData() {
                try {
                    this.showRefreshIndicator(true);
                    
                    const response = await fetch('/api/locations/latest', {
                        headers: {
                            'Authorization': 'Bearer ' + this.getAuthToken(),
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error('Failed to load team data');
                    }
                    
                    const data = await response.json();
                    this.teamData = data.data || [];
                    this.filterTeamData();
                    this.updateLastUpdateTime();
                    
                } catch (error) {
                    console.error('Failed to load team data:', error);
                    this.showError('Failed to load team data: ' + error.message);
                } finally {
                    this.showRefreshIndicator(false);
                }
            }
            
            filterTeamData() {
                const searchTerm = document.getElementById('searchFilter').value.toLowerCase();
                const roleFilter = document.getElementById('roleFilter').value;
                const statusFilter = document.getElementById('statusFilter').value;
                
                this.filteredData = this.teamData.filter(user => {
                    // Search filter
                    if (searchTerm && !user.name.toLowerCase().includes(searchTerm)) {
                        return false;
                    }
                    
                    // Role filter
                    if (roleFilter && user.role !== roleFilter) {
                        return false;
                    }
                    
                    // Status filter
                    if (statusFilter) {
                        const status = this.getUserStatus(user);
                        if (status !== statusFilter) {
                            return false;
                        }
                    }
                    
                    return true;
                });
                
                this.updateTeamList();
                this.updateMapMarkers();
            }
            
            updateTeamList() {
                const teamList = document.getElementById('teamList');
                
                if (this.filteredData.length === 0) {
                    teamList.innerHTML = `
                        <div class="text-center text-gray-500 py-8">
                            <div class="text-4xl mb-4">👥</div>
                            <p>No team members found</p>
                        </div>
                    `;
                    return;
                }
                
                const html = this.filteredData.map(user => {
                    const status = this.getUserStatus(user);
                    const statusClass = `status-${status}`;
                    const roleClass = `role-${user.role?.toLowerCase() || 'mr'}`;
                    const isSelected = this.selectedUserId === user.id;
                    
                    return `
                        <div class="user-item p-3 border-b cursor-pointer ${isSelected ? 'selected' : ''}" 
                             data-user-id="${user.id}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <span class="status-dot ${statusClass}"></span>
                                        <span class="font-medium text-gray-800">${user.name}</span>
                                        <span class="role-badge ${roleClass} ml-2">${user.role || 'MR'}</span>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <div>Last seen: ${user.time_ago}</div>
                                        <div>Accuracy: ${Math.round(user.accuracy || 0)}m</div>
                                    </div>
                                </div>
                                <div class="text-right text-xs text-gray-500">
                                    <div>${user.latitude?.toFixed(4)}</div>
                                    <div>${user.longitude?.toFixed(4)}</div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                
                teamList.innerHTML = html;
                
                // Bind click events
                teamList.querySelectorAll('.user-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const userId = parseInt(item.dataset.userId);
                        this.selectUser(userId);
                    });
                });
            }
            
            updateMapMarkers() {
                // Clear existing markers
                this.markers.forEach(marker => {
                    this.map.removeLayer(marker);
                });
                this.markers.clear();
                
                // Add new markers
                this.filteredData.forEach(user => {
                    if (user.latitude && user.longitude) {
                        const marker = this.createUserMarker(user);
                        this.markers.set(user.id, marker);
                    }
                });
            }
            
            createUserMarker(user) {
                const status = this.getUserStatus(user);
                const color = this.getStatusColor(status);
                
                // Create custom icon
                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `
                        <div style="
                            background-color: ${color};
                            width: 20px;
                            height: 20px;
                            border-radius: 50%;
                            border: 3px solid white;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 10px;
                            font-weight: bold;
                            color: white;
                        ">
                            ${user.name.charAt(0).toUpperCase()}
                        </div>
                    `,
                    iconSize: [26, 26],
                    iconAnchor: [13, 13]
                });
                
                const marker = L.marker([user.latitude, user.longitude], { icon })
                    .addTo(this.map)
                    .bindPopup(this.createPopupContent(user));
                
                marker.on('click', () => {
                    this.selectUser(user.id);
                });
                
                return marker;
            }
            
            createPopupContent(user) {
                const status = this.getUserStatus(user);
                const statusClass = `status-${status}`;
                
                return `
                    <div class="p-2">
                        <div class="font-medium text-gray-800 mb-2">${user.name}</div>
                        <div class="text-sm space-y-1">
                            <div class="flex items-center">
                                <span class="status-dot ${statusClass}"></span>
                                <span class="capitalize">${status}</span>
                            </div>
                            <div><strong>Role:</strong> ${user.role || 'MR'}</div>
                            <div><strong>Last Update:</strong> ${user.time_ago}</div>
                            <div><strong>Accuracy:</strong> ${Math.round(user.accuracy || 0)}m</div>
                            <div><strong>Location:</strong> ${user.latitude?.toFixed(6)}, ${user.longitude?.toFixed(6)}</div>
                        </div>
                    </div>
                `;
            }
            
            selectUser(userId) {
                this.selectedUserId = userId;
                this.updateTeamList();
                
                const user = this.filteredData.find(u => u.id === userId);
                if (user && user.latitude && user.longitude) {
                    this.map.setView([user.latitude, user.longitude], 16);
                    
                    const marker = this.markers.get(userId);
                    if (marker) {
                        marker.openPopup();
                    }
                }
            }
            
            centerMapOnTeam() {
                if (this.filteredData.length === 0) return;
                
                const validLocations = this.filteredData.filter(user => 
                    user.latitude && user.longitude
                );
                
                if (validLocations.length === 0) return;
                
                if (validLocations.length === 1) {
                    const user = validLocations[0];
                    this.map.setView([user.latitude, user.longitude], 16);
                } else {
                    const group = new L.featureGroup(
                        validLocations.map(user => 
                            L.marker([user.latitude, user.longitude])
                        )
                    );
                    this.map.fitBounds(group.getBounds().pad(0.1));
                }
            }
            
            toggleClustering() {
                this.clusteringEnabled = !this.clusteringEnabled;
                // In a real implementation, you would use a clustering library like Leaflet.markercluster
                console.log('Clustering toggled:', this.clusteringEnabled);
            }
            
            getUserStatus(user) {
                if (!user.captured_at) return 'offline';
                
                const now = new Date();
                const lastUpdate = new Date(user.captured_at);
                const minutesAgo = (now - lastUpdate) / (1000 * 60);
                
                if (minutesAgo <= 10) return 'online';
                if (minutesAgo <= 60) return 'away';
                return 'offline';
            }
            
            getStatusColor(status) {
                const colors = {
                    online: '#10b981',
                    away: '#f59e0b',
                    offline: '#ef4444'
                };
                return colors[status] || colors.offline;
            }
            
            startAutoRefresh() {
                // Refresh every 30 seconds
                this.refreshInterval = setInterval(() => {
                    this.loadTeamData();
                }, 30000);
            }
            
            stopAutoRefresh() {
                if (this.refreshInterval) {
                    clearInterval(this.refreshInterval);
                    this.refreshInterval = null;
                }
            }
            
            showRefreshIndicator(show) {
                const indicator = document.getElementById('refreshIndicator');
                if (show) {
                    indicator.className = 'refresh-indicator w-4 h-4 mr-2';
                } else {
                    indicator.className = 'w-4 h-4 mr-2';
                }
            }
            
            updateLastUpdateTime() {
                document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
            }
            
            getAuthToken() {
                // In a real app, this would get the token from localStorage or session
                return localStorage.getItem('auth_token') || 'placeholder-token';
            }
            
            showError(message) {
                // Simple error display - in a real app, you'd use a proper notification system
                console.error(message);
                alert(message);
            }
        }
        
        // Initialize team map when page loads
        document.addEventListener('DOMContentLoaded', () => {
            new TeamMap();
        });
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('Page hidden - pausing auto-refresh');
            } else {
                console.log('Page visible - resuming auto-refresh');
            }
        });
    </script>
</body>
</html>