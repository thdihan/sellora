# Location Tracking Module - Sellora Pharma Sales Force

A complete real-time location tracking system built with **100% free technologies** for pharma sales force management on Namecheap shared hosting.

## 🚀 Technology Stack (All Free)

- **Frontend**: HTML5 Geolocation API + Leaflet.js + OpenStreetMap tiles
- **Backend**: Laravel PHP + MySQL
- **Authentication**: Laravel Sanctum (token-based)
- **Maps**: OpenStreetMap (free raster tiles)
- **PWA**: Service Worker + Web App Manifest
- **Hosting**: Compatible with Namecheap shared hosting

## 📋 Features

### ✅ Real-time Location Tracking
- HTML5 Geolocation with high accuracy
- Automatic position updates every 15-60 seconds
- Screen wake lock support (when available)
- Offline-capable PWA with service worker caching

### ✅ Role-based Access Control
- **MR (Medical Representative)**: Can only track and view own location
- **ASM (Area Sales Manager)**: Can view MR locations in their area
- **RSM (Regional Sales Manager)**: Can view ASM + MR in their region
- **ZSM (Zonal Sales Manager)**: Can view RSM + ASM + MR in their zone
- **NSM (National Sales Manager)**: Can view all locations
- **Admin**: Full access to all data and management

### ✅ Manager Dashboard
- Real-time team map with live location updates
- Filter by role, region, or search by name
- Location accuracy and "last seen" timestamps
- Auto-refresh every 30-60 seconds

### ✅ Security & Privacy
- HTTPS required for geolocation
- Token-based API authentication
- Rate limiting (minimum 10 seconds between updates)
- Server-side validation for all location data
- User consent and on/off tracking controls

## 🛠️ Installation & Setup

### 1. Database Migration

```bash
# Run the location tracking migration
php artisan migrate

# This creates the location_tracking table with:
# - user_id, latitude, longitude, accuracy, captured_at
# - Proper indexes for performance
```

### 2. API Token Setup

#### Option A: Laravel Sanctum (Recommended)
```bash
# Install Sanctum if not already installed
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

# Generate tokens for users
$user = User::find(1);
$token = $user->createToken('location-tracker')->plainTextToken;
echo "API Token: " . $token;
```

#### Option B: Simple API Token (Fallback)
```sql
-- Add api_token column to users table if needed
ALTER TABLE users ADD COLUMN api_token VARCHAR(80) UNIQUE NULL;

-- Generate tokens for users
UPDATE users SET api_token = SHA2(CONCAT(id, email, NOW(), RAND()), 256) WHERE api_token IS NULL;
```

### 3. Configure Environment

```env
# Add to your .env file
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
```

### 4. Register Service Worker

Add to your main layout file:

```html
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(registration => console.log('SW registered'))
        .catch(error => console.log('SW registration failed'));
}
</script>
```

## 🧪 Testing & Validation

### Test Location Tracking (/track)

1. **Access the tracking page**: `https://yourdomain.com/track`
2. **Grant location permissions** when prompted
3. **Click "Start Tracking"** - should see:
   - Green status indicator
   - Your location on the map
   - Coordinates and accuracy displayed
   - Regular position updates

4. **Verify API calls** in browser DevTools:
   - POST requests to `/api/locations` every 15-60 seconds
   - 200 status responses
   - Bearer token in Authorization header

### Test Team Map (/team-map)

1. **Login as manager** (ASM, RSM, ZSM, NSM, or Admin)
2. **Access team map**: `https://yourdomain.com/team-map`
3. **Verify functionality**:
   - Team members listed in sidebar
   - Markers on map for each team member
   - "Last seen X minutes ago" timestamps
   - Auto-refresh every 30 seconds
   - Filter controls working

### Test API Endpoints

```bash
# Test storing location (replace TOKEN with actual token)
curl -X POST https://yourdomain.com/api/locations \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"latitude": 28.6139, "longitude": 77.2090, "accuracy": 10}'

# Test getting latest locations (managers only)
curl -X GET https://yourdomain.com/api/locations/latest \
  -H "Authorization: Bearer TOKEN"

# Test health check
curl -X GET https://yourdomain.com/api/health
```

### Test PWA Installation

1. **Open in Chrome/Edge** on mobile or desktop
2. **Look for install prompt** or "Add to Home Screen"
3. **Install the app** and verify:
   - App icon appears on home screen
   - Opens in standalone mode
   - Works offline (cached pages)
   - Service worker active in DevTools

## ⚠️ Important Limitations & Notes

### PWA Background Tracking

**IMPORTANT**: This PWA does NOT provide true background location tracking when the app is closed. This is by design due to:

- **Browser Security**: Modern browsers heavily restrict background execution
- **Battery Life**: Background tracking drains battery rapidly
- **Privacy Concerns**: Users must explicitly consent to location sharing
- **Platform Limitations**: iOS Safari especially restricts background activity

**What DOES work**:
- ✅ Tracking while app is open and screen is on
- ✅ Tracking continues when switching between app tabs
- ✅ Wake lock prevents screen from sleeping (when supported)
- ✅ Offline caching for when network is unavailable

**What does NOT work**:
- ❌ Tracking when app is completely closed
- ❌ Tracking when device is locked/sleeping
- ❌ Automatic tracking without user interaction

### OpenStreetMap Rate Limits

- **Free tier**: 1 request per second per IP
- **For higher traffic**: Consider switching to MapTiler or MapLibre free tiers
- **Attribution required**: "© OpenStreetMap contributors" must be visible

### Shared Hosting Considerations

- **HTTPS required**: Geolocation API requires secure context
- **PHP version**: Ensure PHP 8.0+ for Laravel compatibility
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Cron jobs**: Set up for Laravel scheduler if needed

## 🔧 Configuration Options

### Tracking Frequency

Edit in `resources/views/location-tracking/track.blade.php`:

```javascript
// Update interval (milliseconds)
const UPDATE_INTERVAL = 30000; // 30 seconds

// Geolocation options
const geoOptions = {
    enableHighAccuracy: true,
    timeout: 10000,
    maximumAge: 5000
};
```

### Map Tile Provider

Edit in view files to switch tile providers:

```javascript
// OpenStreetMap (default)
const tileLayer = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

// Alternative free providers:
// const tileLayer = 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
// const tileLayer = 'https://tile.openstreetmap.de/{z}/{x}/{y}.png';
```

### Rate Limiting

Edit in `app/Http/Controllers/LocationTrackingController.php`:

```php
// Minimum seconds between location updates
const MIN_UPDATE_INTERVAL = 10;
```

## 🐛 Troubleshooting

### Location Not Working

1. **Check HTTPS**: Geolocation requires secure context
2. **Check permissions**: User must grant location access
3. **Check browser support**: Test in Chrome/Firefox/Safari
4. **Check console errors**: Look for JavaScript errors

### API Authentication Failing

1. **Verify token**: Check token is valid and not expired
2. **Check headers**: Ensure `Authorization: Bearer TOKEN` format
3. **Check middleware**: Verify auth middleware is applied
4. **Check CORS**: Ensure API accepts requests from your domain

### Map Not Loading

1. **Check network**: Verify internet connection
2. **Check tile URL**: Ensure OpenStreetMap tiles are accessible
3. **Check attribution**: Ensure proper attribution is displayed
4. **Check rate limits**: Verify not exceeding tile server limits

### PWA Not Installing

1. **Check HTTPS**: PWA requires secure context
2. **Check manifest**: Verify manifest.json is accessible
3. **Check service worker**: Ensure sw.js is registered
4. **Check browser**: Test in Chrome/Edge (best PWA support)

## 📞 Support

For technical support or questions:

- **Documentation**: Check Laravel and Leaflet.js official docs
- **Issues**: Review browser console for error messages
- **Testing**: Use the validation steps above to isolate problems
- **Performance**: Monitor database queries and API response times

## 📄 License

MIT License - Free for commercial and personal use.

---

**Built with ❤️ using 100% free and open-source technologies**