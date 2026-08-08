# Release Runbook — LibxaStack

The process is identical to the framework's, so the canonical runbook is
**[libxa/docs/RELEASING.md](https://github.com/libxa-framework/libxa/blob/main/docs/RELEASING.md)**.
Read that first; this page only covers what differs for the starter kit.

---

## What is different here

### It is a project, not a library

`libxa/libxa` is installed with `composer create-project`, which copies the
whole archive into a new directory. So:

- `composer.lock` **is committed** and must be current. A library omits it; an
  application must pin exactly what it was tested against.
- The `.gitattributes` export-ignore list is deliberately short. Almost
  everything in the repository *is* the deliverable — anything stripped is
  something the new project will not have.
- The `create-project` CI job installs this checkout from scratch and runs the
  generated project's own test suite. That job is the real release gate: it is
  the only check that exercises what a stranger actually receives.

### Its version follows its own timeline

The skeleton and the framework version independently. A framework release does
not require a skeleton release unless the skeleton needs to change — but a
skeleton release should generally follow a framework **minor** release, so new
projects get the new version.

---

## Releasing after a framework release

Order matters, and getting it wrong pins the wrong version in the lock file.

1. **Wait** until the framework version is live on Packagist. Do not start
   before that — Composer will resolve the older release.

2. Widen the constraint in `composer.json`:

   ```json
   "libxa/framework": "^0.9.0 || dev-main@dev"
   ```

   **Keep both halves.** The `dev-main@dev` part is what lets a local sibling
   checkout of the framework be used during development, and the `@dev` suffix
   is what scopes the dev-stability allowance to this one package rather than
   the whole dependency tree. `tests/Feature/FrameworkLinkTest.php` fails the
   build if either is dropped.

3. Update the lock file **without** the local checkout in play, so it records
   the published release rather than `dev-main`:

   ```bash
   mv ../libxaframe ../libxaframe.tmp        # hide the sibling checkout
   composer update libxa/framework
   mv ../libxaframe.tmp ../libxaframe        # restore it
   ```

   Confirm the lock records a real version:

   ```bash
   composer show libxa/framework | head -3   # must NOT say dev-main
   ```

   > Skipping this ships a lock file pinned to `dev-main`, which resolves to
   > whatever `main` happens to be on the day each user installs. That is not a
   > reproducible install.

4. Run the suite, then follow the standard runbook: `release/vX.Y.Z` branch →
   PR into `main` → annotated tag on `main` → back-merge into `develop`.

5. Smoke-test the published result for real:

   ```bash
   composer create-project libxa/libxa /tmp/smoke
   cd /tmp/smoke
   php libxa migrate
   php vendor/bin/phpunit
   php libxa serve
   ```

---

## Release checklist

- [ ] Framework version is live on Packagist (if this follows a framework release)
- [ ] `composer.json` constraint widened, both halves kept
- [ ] `composer.lock` regenerated **without** the sibling checkout, and records
      a published version rather than `dev-main`
- [ ] `composer check` passes
- [ ] `CHANGELOG.md` has a section for this version
- [ ] The `create-project` CI job is green
- [ ] Tag is annotated and cut from `main`
- [ ] Packagist shows the new version
- [ ] `main` back-merged into `develop`
