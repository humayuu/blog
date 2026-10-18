<?php

namespace Tests\Feature;

use App\Exceptions\Auth\InvalidOtp;
use App\Models\User;
use App\Services\V1\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'name' => 'Test User',
        'email' => 'Test@Example.com',
        'password' => 'Str0ngPassw0rdX!z',
        'password_confirmation' => 'Str0ngPassw0rdX!z',
    ];

    private function register(): array
    {
        $res = $this->postJson('/api/v1/register', $this->payload)->assertCreated();

        return [User::first(), $res->json('token')];
    }

    private function otpFor(User $user, string $otp): void
    {
        Cache::put('otp_'.$user->id, hash_hmac('sha256', $otp, config('app.key')), 600);
    }

    public function test_authenticated_route_works_with_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('t')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/me')->assertOk();
    }

    public function test_register_normalizes_email_and_rejects_weak_password(): void
    {
        $this->postJson('/api/v1/register', [...$this->payload, 'password' => 'password', 'password_confirmation' => 'password'])
            ->assertUnprocessable()->assertJsonValidationErrors('password');

        $this->register();
        $this->assertSame('test@example.com', User::first()->email);
    }

    public function test_otp_is_cached_hashed_not_plaintext(): void
    {
        Queue::fake();
        [$user] = $this->register();

        $cached = Cache::get('otp_'.$user->id);
        $this->assertSame(64, strlen($cached));
    }

    public function test_otp_locks_after_five_wrong_attempts(): void
    {
        Queue::fake();
        [$user] = $this->register();
        $this->otpFor($user, '123456');
        $service = app(AuthService::class);

        // Call the service directly: the HTTP throttle (5/min) would trip first.
        for ($i = 0; $i < 5; $i++) {
            try {
                $service->verifyEmail('000000', $user);
            } catch (InvalidOtp) {
            }
        }

        $this->expectException(InvalidOtp::class);

        try {
            $service->verifyEmail('123456', $user);
        } finally {
            $this->assertNull($user->fresh()->email_verified_at);
        }
    }

    public function test_correct_otp_verifies_email(): void
    {
        Queue::fake();
        [$user, $token] = $this->register();
        $this->otpFor($user, '123456');

        $this->withToken($token)->postJson('/api/v1/verify', ['otp' => '123456'])->assertOk();
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_resend_has_cooldown_and_blocks_verified_users(): void
    {
        Queue::fake();
        [$user, $token] = $this->register();

        $this->withToken($token)->postJson('/api/v1/resend-otp')->assertOk();
        $this->withToken($token)->postJson('/api/v1/resend-otp')->assertStatus(429);

    }

    public function test_resend_is_rejected_for_verified_users(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('t')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/resend-otp')->assertStatus(400);
    }

    public function test_login_is_throttled_per_email(): void
    {
        User::factory()->create(['email' => 'a@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/login', ['email' => 'a@example.com', 'password' => 'wrong'])->assertUnauthorized();
        }
        $this->postJson('/api/v1/login', ['email' => 'a@example.com', 'password' => 'wrong'])->assertStatus(429);
    }

    public function test_unverified_user_blocked_from_me(): void
    {
        $user = User::factory()->unverified()->create();
        $token = $user->createToken('t')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/me')->assertForbidden();
    }
}
