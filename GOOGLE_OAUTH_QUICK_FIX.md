# Quick Fix for "OAuth client was not found" Error

## The Problem
You're getting this error because the Google OAuth credentials in `config/google_config.php` are still placeholder values:
- `GOOGLE_CLIENT_ID` = 'YOUR_GOOGLE_CLIENT_ID'
- `GOOGLE_CLIENT_SECRET` = 'YOUR_GOOGLE_CLIENT_SECRET'

## Solution: Get Real Google OAuth Credentials

### Step 1: Go to Google Cloud Console
1. Visit: https://console.cloud.google.com/
2. Sign in with your Google account (samriddhisrivastava9683@gmail.com)

### Step 2: Create or Select Project
1. Click on the project dropdown at the top
2. Click "New Project" or select an existing one
3. Give it a name like "ELMS Project"

### Step 3: Enable APIs
1. Go to "APIs & Services" > "Library"
2. Search for and enable these APIs:
   - "Google+ API" (or "Google Identity API")
   - "Google OAuth2 API"

### Step 4: Configure OAuth Consent Screen
1. Go to "APIs & Services" > "OAuth consent screen"
2. Choose "External" user type
3. Fill in required fields:
   - App name: "ELMS - E-Learning Management System"
   - User support email: samriddhisrivastava9683@gmail.com
   - Developer contact information: samriddhisrivastava9683@gmail.com
4. Add scopes: `email`, `profile`, `openid`
5. Add test users: samriddhisrivastava9683@gmail.com

### Step 5: Create OAuth Credentials
1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "OAuth 2.0 Client IDs"
3. Choose "Web application"
4. Set:
   - Name: "ELMS Web Client"
   - Authorized JavaScript origins:
     - `http://localhost`
   - Authorized redirect URIs:
     - `http://localhost/elms/auth/google-callback.php`

### Step 6: Copy Credentials
1. After creating, you'll see:
   - **Client ID** (looks like: `123456789-abcdefghijklmnopqrstuvwxyz.apps.googleusercontent.com`)
   - **Client Secret** (looks like: `GOCSPX-abcdefghijklmnopqrstuvwxyz`)

### Step 7: Update Configuration
Edit `config/google_config.php` and replace:
```php
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
```

With your actual credentials:
```php
define('GOOGLE_CLIENT_ID', '123456789-abcdefghijklmnopqrstuvwxyz.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'GOCSPX-your-actual-secret-here');
```

### Step 8: Test
1. Visit: http://localhost/elms/test_google_oauth.php
2. Check if credentials are now "Set" instead of "Not Set"
3. Try the Google login again

## Common Issues

### "Access blocked" Error
- Make sure you added samriddhisrivastava9683@gmail.com as a test user
- The app should be in "Testing" mode, not "Production"

### "Invalid redirect_uri" Error
- Make sure the redirect URI in Google Console exactly matches: `http://localhost/elms/auth/google-callback.php`
- No trailing slashes, correct port number

### "OAuth client was not found" Error
- Double-check that you copied the Client ID and Secret correctly
- Make sure there are no extra spaces or characters

## Quick Test
After updating the credentials, visit:
http://localhost/elms/test_google_oauth.php

You should see:
- ✅ Google Client ID: Set
- ✅ Google Client Secret: Set
- ✅ cURL Extension: Available
- ✅ Database Connection: Connected 