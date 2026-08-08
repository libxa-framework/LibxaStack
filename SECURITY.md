# Security Policy

LibxaStack is the application skeleton. Most security-relevant code lives in
the framework — if the issue is in routing, the ORM, Blade, sessions or
encryption, report it against
[libxa-framework/libxa](https://github.com/libxa-framework/libxa/security/policy)
instead.

Report it **here** when the problem is an insecure default that ships with the
skeleton: a config value, an example controller, a migration, or the
`.env.example`.

## Supported versions

| Version | Supported |
|---|---|
| Latest release | ✅ |
| Anything older | ❌ — the skeleton is a starting point, not a maintained runtime |

Once you run `composer create-project`, the code is **yours**. We cannot patch
your application; we can only fix the template so new projects start correct.
When a skeleton fix matters for existing projects, the advisory will say
explicitly what to change in your own copy.

## Reporting a vulnerability

**Do not open a public issue, pull request, or discussion.**

- **GitHub Security Advisory** *(preferred)* —
  [Report a vulnerability](https://github.com/libxa-framework/LibxaStack/security/advisories/new)
- **Email** — `libxa@vyloxi.com`, subject prefixed `[SECURITY]`

Include the affected version, the impact, and the smallest reproduction you
can manage.

| Stage | Target |
|---|---|
| Acknowledgement | 48 hours |
| Initial assessment | 5 working days |
| Fix released, or a plan with a date | 30 days |

## Before deploying your own project

The skeleton ships development-friendly defaults. Change these before any
production deploy — a checklist, not a promise that this is exhaustive:

- [ ] `APP_DEBUG=false`. With it on, the error page deliberately shows stack
      traces, file paths and environment details.
- [ ] `APP_ENV=production`
- [ ] `php libxa key:generate` has been run, and `APP_KEY` is **not** the value
      from any tutorial or shared between environments. Anyone holding your key
      can forge encrypted payloads.
- [ ] `.env` is not committed and is not reachable over HTTP. Your web root is
      `src/public/` — serve only that directory.
- [ ] `SESSION_SECURE_COOKIE=true` when serving over HTTPS
- [ ] `TRUSTED_PROXIES` set **only** if you run a reverse proxy, and listing its
      addresses. Leaving it empty is correct and safe; setting it to `*` lets
      anyone spoof their client IP and bypass rate limiting.
- [ ] Database credentials come from the environment, not from a committed file
- [ ] `src/storage/` and `database/` are writable by the web user and **not**
      served publicly
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php libxa config:cache && php libxa route:cache && php libxa view:cache`
- [ ] Rate limiting left enabled (`RATE_LIMIT_ENABLED` unset or `true`)
- [ ] Routes exempted from CSRF via `session.csrf_except` verify authenticity
      another way — a webhook signature, for example

## Out of scope

- Vulnerabilities in **your** application code after `create-project`
- Anything requiring `APP_DEBUG=true`, which is documented as development-only
- The development defaults themselves being development defaults — but a
  default that is *dangerous even in development*, or one that is easy to ship
  to production by accident, is very much in scope. Please report those.
