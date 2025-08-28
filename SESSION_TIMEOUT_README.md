# PEL-Abacus Session Timeout Configuration

## Overview
The PEL-Abacus application now has a 4-hour session timeout with automatic logout functionality and user-friendly warnings.

## Configuration

### Session Lifetime
- **Duration**: 4 hours (240 minutes)
- **Configuration**: Set in `.env` file as `SESSION_LIFETIME=240`
- **Location**: `config/session.php` reads from environment variable

### Features Implemented

#### 1. Automatic Session Timeout
- Sessions automatically expire after 4 hours of inactivity
- Users are automatically logged out and redirected to login page
- Works for both regular page requests and AJAX calls

#### 2. Session Warning System
- Warning appears 15 minutes before session expiry
- Shows remaining time in minutes
- Non-intrusive alert in top-right corner
- Auto-dismisses after user interaction

#### 3. AJAX Session Handling
- Automatic handling of session timeouts in AJAX requests
- Returns proper JSON responses for expired sessions
- Seamless user experience without page refreshes

#### 4. Activity Tracking
- Tracks user activity (mouse, keyboard, touch events)
- Resets session timer on any user interaction
- Uses efficient event listeners with passive mode

## Files Modified/Created

### Configuration Files
- `.env` - Updated SESSION_LIFETIME to 240 minutes
- `config/session.php` - Session configuration (uses env variable)

### Backend Files
- `app/Http/Middleware/SessionTimeoutMiddleware.php` - Main session timeout logic
- `bootstrap/app.php` - Middleware registration
- `app/Http/Controllers/ProfileController.php` - Password change functionality

### Frontend Files
- `resources/views/layouts/app.blade.php` - Session warning display and AJAX handling
- `resources/views/profile.blade.php` - Password change form

## How It Works

### Session Flow
1. User logs in → Session starts with 4-hour timer
2. User activity → Timer resets on each interaction
3. 15 minutes before expiry → Warning appears
4. At expiry → Automatic logout and redirect
5. AJAX requests during expiry → JSON response with redirect URL

### Security Features
- **CSRF Protection**: All requests protected with tokens
- **Secure Logout**: Session flushed and regenerated on timeout
- **Activity Monitoring**: Real-time activity tracking
- **AJAX Security**: Proper handling of expired sessions in AJAX calls

### User Experience
- **Non-intrusive Warnings**: Session warnings don't interrupt workflow
- **Auto-redirect**: Seamless redirect to login after timeout
- **Activity Reset**: Any user interaction keeps session alive
- **Mobile Friendly**: Works on all devices and screen sizes

## Testing

### Manual Testing
1. Login to the application
2. Wait for session warning (3 hours 45 minutes)
3. Verify warning appears with correct countdown
4. Continue using app to reset timer
5. Wait for automatic logout (4 hours total)
6. Verify redirect to login page

### AJAX Testing
1. Make AJAX requests near session expiry
2. Verify proper JSON responses for expired sessions
3. Test automatic redirect handling

## Customization

### Change Session Duration
Edit `.env` file:
```env
SESSION_LIFETIME=240  # Change to desired minutes
```

### Adjust Warning Time
Edit `SessionTimeoutMiddleware.php`:
```php
$warningTime = 15 * 60; // Change warning time (in seconds)
```

### Modify Warning Appearance
Edit `resources/views/layouts/app.blade.php`:
```css
/* Customize warning alert styles */
.alert-warning {
    /* Your custom styles */
}
```

## Troubleshooting

### Common Issues
1. **Session not timing out**: Check SESSION_LIFETIME in .env
2. **Warning not appearing**: Verify middleware is registered in bootstrap/app.php
3. **AJAX not handling timeout**: Check CSRF token in AJAX requests

### Debug Mode
Enable debug logging in `.env`:
```env
LOG_LEVEL=debug
```

Check logs in `storage/logs/laravel.log` for session timeout events.

## Security Considerations

- Session data is encrypted if SESSION_ENCRYPT=true
- CSRF tokens prevent cross-site request forgery
- Session regeneration prevents session fixation attacks
- Activity tracking prevents unauthorized session extension

## Performance Impact

- **Minimal Overhead**: Activity tracking uses passive event listeners
- **Efficient Storage**: Session data stored in configured driver (database/file)
- **Optimized Queries**: Minimal database impact for session management
- **Browser Compatibility**: Works across all modern browsers

---

**Note**: This session timeout system enhances security while maintaining excellent user experience. Users are warned before logout and can continue working seamlessly.