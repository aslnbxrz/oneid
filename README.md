# OneID Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aslnbxrz/oneid.svg?style=flat-square)](https://packagist.org/packages/aslnbxrz/oneid)
[![Total Downloads](https://img.shields.io/packagist/dt/aslnbxrz/oneid.svg?style=flat-square)](https://packagist.org/packages/aslnbxrz/oneid)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/aslnbxrz/oneid/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/aslnbxrz/oneid/actions)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

Professional Laravel package for integrating with **OneID** - Uzbekistan's unified identification system (Yagona identifikatsiya tizimi). This package provides a clean, secure, and easy-to-use interface for OneID authentication in Laravel applications.

## 🚀 Features

- **Easy Integration**: Simple setup and configuration
- **Flexible Routes**: Use built-in routes or create your own
- **Facade Support**: Clean facade interface for easy usage
- **Comprehensive Configuration**: Extensive configuration options
- **Error Handling**: Robust error handling and logging
- **Security**: Built-in security features and validation
- **Laravel 10+ Support**: Compatible with modern Laravel versions
- **Testing**: Comprehensive test suite included

## 📋 Requirements

- PHP 8.2 or higher
- Laravel 10.0 or higher
- Valid OneID application credentials

## 📦 Installation

You can install the package via Composer:

```bash
composer require aslnbxrz/oneid
```

## ⚙️ Configuration

### 1. Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Aslnbxrz\OneId\OneIdServiceProvider" --tag="oneid-config"
```

### 2. Environment Variables

Add the following environment variables to your `.env` file:

```env
# OneID Configuration
ONEID_BASE_URL=https://sso.egov.uz
ONEID_CLIENT_ID=your_client_id
ONEID_CLIENT_SECRET=your_client_secret
ONEID_SCOPE=openid profile
ONEID_REDIRECT_URI=https://your-app.com/auth/oneid/callback

# Optional: Route Configuration
ONEID_ROUTES_ENABLED=true
ONEID_ROUTE_PREFIX=auth/oneid
ONEID_ROUTE_MIDDLEWARE=web

# Optional: Logging Configuration
ONEID_LOGGING_ENABLED=true
ONEID_LOG_LEVEL=info
ONEID_LOG_CHANNEL=default

# Optional: Security Configuration
ONEID_VERIFY_SSL=true
ONEID_RATE_LIMITING_ENABLED=true
ONEID_RATE_LIMIT_ATTEMPTS=5
ONEID_RATE_LIMIT_DECAY=1
```

### 3. OneID Application Setup

1. Register your application at [OneID Portal](https://sso.egov.uz)
2. Get your `client_id` and `client_secret`
3. Set up your redirect URI to match your application

## 🎯 Usage

### Using Facade (Recommended)

```php
use Aslnbxrz\OneId\Facades\OneID;

// Generate authorization URL
$authUrl = OneID::getAuthorizationUrl();

// Handle authentication callback
$result = OneID::handle($code);

// Check if authentication was successful
if ($result->success) {
    $userData = $result->getUserData();
    
    // Basic user information
    $pin = $userData->pin;
    $firstName = $userData->first_name;
    $lastName = $userData->sur_name;
    $middleName = $userData->mid_name;
    $fullName = $userData->getFullName();
    
    // Additional information
    $isVerified = $userData->isVerified();
    $userType = $userData->user_type; // 'I' for Individual, 'L' for Legal
    $authMethod = $userData->getAuthMethodName();
    $birthDate = $userData->getBirthDate();
    
    // Legal entity information (if applicable)
    if ($userData->isLegalEntity()) {
        $legalEntities = $userData->legal_info;
        $basicLegalEntity = $userData->getBasicLegalEntity();
    }
    
    // Process user data...
} else {
    // Handle authentication failure
    $error = $result->error;
    $message = $result->message;
}

// Logout user
$logoutResult = OneID::logout($accessToken);

// Validate token
$isValid = OneID::validateToken($accessToken);

// Get user info
$userInfo = OneID::getUserInfo($accessToken);

// Check configuration
$isConfigured = OneID::isConfigured();
$configErrors = OneID::getConfigurationErrors();
```

### Using Routes (Optional)

If you have routes enabled, you can use the built-in endpoints:

#### 1. Redirect to OneID

```php
// Redirect user to OneID authorization
return redirect()->route('oneid.redirect');
```

#### 2. Handle Callback

Create a callback route in your application:

```php
// routes/web.php
Route::get('/auth/oneid/callback', function (Request $request) {
    $code = $request->get('code');
    
    if (!$code) {
        return redirect('/login')->with('error', 'Authorization code not provided');
    }
    
    $result = OneID::handle($code);
    
    if ($result->success) {
        // Handle successful authentication
        $userData = $result->data;
        
        // Create or update user in your database
        $user = User::updateOrCreate(
            ['pin' => $userData['pin']],
            [
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'middle_name' => $userData['middle_name'] ?? null,
                'birth_date' => $userData['birth_date'] ?? null,
                'gender' => $userData['gender'] ?? null,
            ]
        );
        
        Auth::login($user);
        
        return redirect('/dashboard');
    } else {
        return redirect('/login')->with('error', $result->message);
    }
})->name('auth.oneid.callback');
```

#### 3. Logout

```php
// Logout from OneID
Route::post('/auth/oneid/logout', function (Request $request) {
    $accessToken = $request->get('access_token');
    
    if ($accessToken) {
        $result = OneID::logout($accessToken);
        
        if ($result->success) {
            Auth::logout();
            return response()->json(['message' => 'Logged out successfully']);
        }
    }
    
    return response()->json(['error' => 'Logout failed'], 400);
});
```

### Custom Routes

If you prefer to disable built-in routes and create your own:

```env
ONEID_ROUTES_ENABLED=false
```

Then create your own routes:

```php
// routes/web.php
Route::get('/auth/oneid', function () {
    return redirect(OneID::getAuthorizationUrl());
})->name('auth.oneid');

Route::post('/auth/oneid/handle', function (Request $request) {
    $result = OneID::handle($request->get('code'));
    return response()->json($result->toArray());
})->name('auth.oneid.handle');

Route::post('/auth/oneid/logout', function (Request $request) {
    $result = OneID::logout($request->get('access_token'));
    return response()->json($result->toArray());
})->name('auth.oneid.logout');
```

## 🔧 Configuration Options

### Main Configuration

| Option | Description | Default |
|--------|-------------|---------|
| `base_url` | OneID API base URL | `https://sso.egov.uz` |
| `client_id` | Your OneID client ID | Required |
| `client_secret` | Your OneID client secret | Required |
| `scope` | Requested permissions | `openid profile` |
| `redirect_uri` | Callback URL | Required |

### Route Configuration

| Option | Description | Default |
|--------|-------------|---------|
| `routes.enabled` | Enable/disable built-in routes | `true` |
| `routes.prefix` | Route prefix | `auth/oneid` |
| `routes.middleware` | Route middleware | `web` |
| `routes.names` | Route names | Custom names |

### User Data Configuration

| Option | Description | Default |
|--------|-------------|---------|
| `user.pin_field` | PIN field name | `pin` |
| `user.required_fields` | Required user fields | `['pin', 'first_name', 'last_name', 'middle_name']` |
| `user.optional_fields` | Optional user fields | `['birth_date', 'gender', 'nationality', 'region', 'district']` |

### Security Configuration

| Option | Description | Default |
|--------|-------------|---------|
| `security.verify_ssl` | Verify SSL certificates | `true` |
| `security.rate_limiting.enabled` | Enable rate limiting | `true` |
| `security.rate_limiting.max_attempts` | Max attempts per minute | `5` |

## 🔒 Security Features

- **CSRF Protection**: Built-in CSRF token validation
- **Rate Limiting**: Configurable rate limiting for API calls
- **SSL Verification**: SSL certificate verification by default
- **Input Validation**: Comprehensive input validation
- **Secure Logging**: Structured logging with sensitive data protection

## 📊 Error Handling

The package provides comprehensive error handling:

```php
$result = OneID::handle($code);

if (!$result->success) {
    switch ($result->error) {
        case 'token_null':
            // Could not obtain access token
            break;
        case 'missing_key':
            // Required user data missing
            break;
        default:
            // Other errors
            break;
    }
}
```

## 🧪 Testing

Run the tests with:

```bash
composer test
```

Run tests with coverage:

```bash
composer test:coverage
```

## 📝 Logging

The package logs all important events. You can configure logging in your `config/oneid.php`:

```php
'logging' => [
    'enabled' => true,
    'level' => 'info',
    'channel' => 'default',
],
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

If you encounter any issues or have questions:

1. Check the [Issues](https://github.com/aslnbxrz/oneid/issues) page
2. Create a new issue if your problem isn't already reported
3. Contact the maintainer: [bexruz.aslonov1@gmail.com](mailto:bexruz.aslonov1@gmail.com)

## 🙏 Acknowledgments

- [OneID System](https://e-gov.uz/projects/one-id) - Uzbekistan's unified identification system
- [Laravel](https://laravel.com) - The PHP framework
- [Spatie](https://spatie.be) - For the excellent Laravel package tools
- [Saloon](https://saloon.dev) - For the HTTP client library

## 📈 Changelog

### v1.0.0
- Initial release with professional architecture
- Enhanced configuration system
- Optional route system
- Comprehensive error handling
- Security improvements
- Complete documentation
- Full test coverage
- CLI validation command
- Simple facade interface
