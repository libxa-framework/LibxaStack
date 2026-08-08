<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * End-to-end coverage of the flow every new project touches first:
 * register → login → protected page → logout.
 */
class AuthFlowTest extends TestCase
{
    private function credentials(array $overrides = []): array
    {
        return array_merge([
            'name'                  => 'Ada Lovelace',
            'email'                 => 'ada' . random_int(1000, 999999) . '@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    // ── Public pages ──────────────────────────────────────────────────

    public function test_the_welcome_page_renders(): void
    {
        $response = $this->get('/');

        $this->assertSame(200, $response->getStatus());
        $this->assertNotSame('', $response->getContent());
    }

    /**
     * layouts/app.blade.php uses @hasSection, a directive the compiler did
     * not implement — so the compiled cache file was invalid PHP and every
     * page using the default layout was a fatal parse error.
     */
    public function test_the_login_page_renders_through_the_default_layout(): void
    {
        $response = $this->get('/login');

        $this->assertSame(200, $response->getStatus());
        $this->assertStringContainsString('<form', $response->getContent());
        $this->assertStringNotContainsString('@hasSection', $response->getContent());
    }

    public function test_the_register_page_renders(): void
    {
        $this->assertSame(200, $this->get('/register')->getStatus());
    }

    public function test_an_unknown_url_is_404(): void
    {
        $this->assertSame(404, $this->get('/no-such-page')->getStatus());
    }

    // ── Registration ──────────────────────────────────────────────────

    public function test_a_user_can_register(): void
    {
        $data = $this->credentials();

        $response = $this->post('/register', $data);

        $this->assertSame(302, $response->getStatus());
        $this->assertDatabaseHas('users', ['email' => $data['email']]);
    }

    public function test_the_password_is_hashed_not_stored_in_plain_text(): void
    {
        $data = $this->credentials();

        $this->post('/register', $data);

        $stmt = $this->pdo()->prepare('SELECT password FROM users WHERE email = ?');
        $stmt->execute([$data['email']]);
        $stored = (string) $stmt->fetchColumn();

        $this->assertNotSame($data['password'], $stored);
        $this->assertTrue(password_verify($data['password'], $stored));
    }

    public function test_registration_rejects_an_invalid_email(): void
    {
        $data = $this->credentials(['email' => 'not-an-email']);

        $this->post('/register', $data);

        $this->assertDatabaseMissing('users', ['email' => 'not-an-email']);
    }

    public function test_registration_rejects_a_mismatched_confirmation(): void
    {
        $data = $this->credentials(['password_confirmation' => 'something-else']);

        $this->post('/register', $data);

        $this->assertDatabaseMissing('users', ['email' => $data['email']]);
    }

    public function test_registration_rejects_a_duplicate_email(): void
    {
        $data = $this->credentials();

        $this->post('/register', $data);
        $this->assertDatabaseHas('users', ['email' => $data['email']]);

        $this->post('/register', $this->credentials(['email' => $data['email']]));

        $stmt = $this->pdo()->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $stmt->execute([$data['email']]);

        $this->assertSame(1, (int) $stmt->fetchColumn(), 'the unique rule must not let a duplicate through');
    }

    // ── Login ─────────────────────────────────────────────────────────

    public function test_a_registered_user_can_log_in_and_reach_a_protected_page(): void
    {
        $data = $this->credentials();
        $this->post('/register', $data);

        $login = $this->post('/login', ['email' => $data['email'], 'password' => $data['password']]);
        $this->assertSame(302, $login->getStatus());

        $home = $this->get('/home');
        $this->assertSame(200, $home->getStatus());
    }

    public function test_a_wrong_password_does_not_authenticate(): void
    {
        $data = $this->credentials();
        $this->post('/register', $data);

        $this->post('/logout');
        $_SESSION = [];

        $this->post('/login', ['email' => $data['email'], 'password' => 'definitely-wrong']);

        $this->assertSame(302, $this->get('/home')->getStatus(), 'a failed login must not grant access');
    }

    public function test_a_guest_is_redirected_away_from_a_protected_page(): void
    {
        $this->assertSame(302, $this->get('/home')->getStatus());
    }

    // ── CSRF ──────────────────────────────────────────────────────────

    public function test_a_post_without_a_csrf_token_is_rejected(): void
    {
        $response = $this->call('POST', '/login', ['email' => 'a@b.co', 'password' => 'x']);

        $this->assertSame(419, $response->getStatus());
    }

    /**
     * `_token[]=x` reached hash_equals() with an array, so an attacker could
     * turn any form endpoint into a 500 instead of a clean 419.
     */
    public function test_an_array_csrf_token_is_a_419_not_a_500(): void
    {
        $response = $this->call('POST', '/login', ['_token' => ['x'], 'email' => 'a@b.co']);

        $this->assertSame(419, $response->getStatus());
    }

    // ── API ───────────────────────────────────────────────────────────

    public function test_the_public_api_endpoint_returns_json(): void
    {
        $response = $this->get('/api/ping', ['Accept' => 'application/json']);

        $this->assertSame(200, $response->getStatus());
        $this->assertSame('ok', json_decode($response->getContent(), true)['status'] ?? null);
    }

    public function test_the_protected_api_endpoint_rejects_an_anonymous_caller(): void
    {
        $response = $this->get('/api/user', ['Accept' => 'application/json']);

        $this->assertSame(401, $response->getStatus());
        $this->assertIsArray(json_decode($response->getContent(), true));
    }

    public function test_the_protected_api_endpoint_rejects_a_bogus_bearer_token(): void
    {
        $response = $this->get('/api/user', [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer not-a-real-token',
        ]);

        $this->assertSame(401, $response->getStatus());
    }
}
