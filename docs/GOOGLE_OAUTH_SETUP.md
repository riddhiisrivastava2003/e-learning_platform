# Google OAuth Setup Guide for ELMS

## Prerequisites
- Google Cloud Console account
- XAMPP/WAMP server running
- PHP with cURL extension enabled

## Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google+ API and Google OAuth2 API

## Step 2: Configure OAuth Consent Screen

1. Go to "APIs & Services" > "OAuth consent screen"
2. Choose "External" user type
3. Fill in the required information:
   - App name: "ELMS - E-Learning Management System"
   - User support email: Your email
   - Developer contact information: Your email
4. Add scopes:
   - `email`
   - `profile`
   - `openid`
5. Add test users (your email addresses)

## Step 3: Create OAuth 2.0 Credentials

1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "OAuth 2.0 Client IDs"
3. Choose "Web application"
4. Set the following:
   - Name: "ELMS Web Client"
   - Authorized JavaScript origins:
     - `http://localhost`
     - `http://localhost:8080` (if using different port)
   - Authorized redirect URIs:
     - `http://localhost/elms/auth/google-callback.php`
     - `http://localhost:8080/elms/auth/google-callback.php` (if using different port)

## Step 4: Update Configuration

1. Copy the Client ID and Client Secret
2. Open `config/google_config.php`
3. Replace the placeholder values:
   ```php
   define('GOOGLE_CLIENT_ID', 'your-actual-client-id-here');
   define('GOOGLE_CLIENT_SECRET', 'your-actual-client-secret-here');
   ```

## Step 5: Test the Setup

1. Start your XAMPP/WAMP server
2. Navigate to `http://localhost/elms/`
3. Click "Continue with Google"
4. You should be redirected to Google's OAuth consent screen
5. After authorization, you'll be redirected back to select your role

## Troubleshooting

### Common Issues:

1. **"Invalid redirect_uri" error**
   - Make sure the redirect URI in Google Console exactly matches your callback URL
   - Check for trailing slashes or port differences

2. **"Access blocked" error**
   - Add your email to test users in OAuth consent screen
   - Make sure the app is not in production mode

3. **"cURL not available" error**
   - Enable cURL extension in PHP
   - In XAMPP: Edit php.ini and uncomment `extension=curl`

4. **Database errors**
   - Make sure the `users` table has `google_id` column
   - Import the complete database schema

### Testing Commands:

```bash
# Check if cURL is enabled
php -m | grep curl

# Test Google config
php -r "require 'config/google_config.php'; echo 'Config loaded successfully';"
```

## Security Notes

1. Never commit your actual Google credentials to version control
2. Use environment variables in production
3. Regularly rotate your OAuth credentials
4. Monitor OAuth usage in Google Cloud Console

## Production Deployment

For production, update the redirect URIs to your actual domain:
- `https://yourdomain.com/auth/google-callback.php`
- Update the `GOOGLE_REDIRECT_URI` constant accordingly 