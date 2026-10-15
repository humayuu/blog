# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Laravel 13 (PHP 8.3+) JSON API backend for a blog. Auth uses Laravel Sanctum bearer tokens, with OTP-based email verification. `spatie/laravel-permission` (roles/permissions) and Laravel Telescope are installed. Local env uses MySQL (`blog_db`), with the `database` driver for queue and cache.

## Commands

```bash
composer setup                 # install deps, copy .env, key:generate, migrate, build assets
composer dev                   # runs artisan serve + queue:listen + vite concurrently
composer test                  # clears config cache, then runs php artisan test
php artisan test --filter=TestName            # single test / method
php artisan test tests/Feature/SomeTest.php   # single file
vendor/bin/pint                # format code (Laravel Pint)
php artisan queue:work         # needed for OTP emails to actually send outside `composer dev`
```

Tests run against in-memory SQLite, with `QUEUE_CONNECTION=sync`, `CACHE_STORE=array` and `MAIL_MAILER=array` (see `phpunit.xml`).

## Architecture

**Versioned API layering.** Everything API-related lives under a `V1` namespace: `routes/api.php` (`/api/v1/...` prefix), `Http/Controllers/Api/V1`, `Http/Requests/V1`, `Http/Resources/V1`, `Services/V1`. New features should follow the same split:
- **FormRequest** validates input. The controller passes `$request->validated()` to the service.
- **Service** (`Services/V1/*Service`) holds business logic and is injected into the controller via the constructor.
- **Controller** only shapes the response: `response()->json(['status' => bool, 'message' => ..., ...])`, with models wrapped in an API Resource.

**Errors as self-rendering exceptions.** Domain failures throw custom exceptions in `app/Exceptions/<Domain>/` that define their own `render()` returning the `{status: false, message}` JSON shape (e.g. `InvalidCredentials` → 401, `InvalidOtp` → 400). `bootstrap/app.php` forces JSON rendering for `api/*` requests.

**Auth / email-verification flow.**
1. `register` creates the user, generates a 6-digit OTP, stores it in the cache as `otp_{user_id}` (10 min TTL), and dispatches the queued `SendOtpEmail` job (→ `Mail/Auth/OtpMail`, view `resources/views/otp_mail.blade.php`). It returns a Sanctum token right away.
2. `POST /verify` (requires `auth:sanctum`) checks the OTP with `hash_equals`, sets `email_verified_at`, and clears the cache key.
3. Routes that need a verified email use the `verified` middleware alias. **This alias is overridden** in `bootstrap/app.php` to point at `App\Http\Middleware\Auth\EnsureAccountVerified`, which returns a JSON 403. It does not use Laravel's built-in `MustVerifyEmail` (the `User` model doesn't implement it).

Auth endpoints use `throttle:5,1`.

**Model conventions.** `User` declares mass-assignment and hidden fields with PHP attributes (`#[Fillable]`, `#[Hidden]`) rather than `$fillable`/`$hidden` properties.
