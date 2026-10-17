# Security

This page explains how the store protects itself, one point at a time.
Each point says what the risk is, what stops it, and where to find it in
the code. The tests named under each point prove it works; run them all
with `php artisan test`.

## At a glance

| Risk | What protects the store |
| --- | --- |
| SQL injection | Eloquent sends all input as bound values, never as SQL |
| Cross-site scripting | Blade `{{ }}` escapes every value, plus a strict Content Security Policy |
| Cross-site request forgery | A `@csrf` token on every form, and logout is POST only |
| Stolen passwords | Passwords are stored only as bcrypt hashes |
| Bad form data | A Form Request validates every form, and only validated fields are saved |
| Harmful uploads | JPG, PNG or WebP only, checked by content, 2 MB max, saved under a random name |
| Break-ins at the login | Five tries then a lockout, a new session on login, admins only |
| Admin pages without logging in | `auth` and `admin` middleware on every admin route |
| Leaked secrets | Secrets live only in `.env`, which is never committed |

The sections below explain each one.

## 1. SQL injection

**Risk:** someone types SQL into the search box or a web address to read or
change the database.

**What stops it:** every query goes through Laravel's Eloquent query
builder, which sends user input to MySQL as a bound parameter, never as
part of the SQL text. The search also escapes the LIKE wildcards `%` and
`_`, so searching for `100%` finds exactly that instead of everything.
Sort and category values are checked against a fixed list before they are
used.

**Where:** `app/Catalog/Catalog.php` (`find()`),
`app/Http/Requests/ProductListingRequest.php`.

**Tests:** `test_search_treats_sql_as_plain_text`,
`test_search_treats_wildcards_as_plain_text`.

## 2. Cross-site scripting (XSS)

**Risk:** someone saves `<script>` in a product name or search term, and it
runs in another visitor's browser.

**What stops it:** every value on every page is printed with Blade's
`{{ }}`, which turns `<` and `>` into harmless text. The raw `{!! !!}` form
is not used anywhere. As a second layer, every page sends a strict
Content Security Policy that only allows scripts from the site itself, so
even an injected inline script would be blocked by the browser.

**Where:** `resources/views/`, `app/Http/Middleware/SecurityHeaders.php`.

**Tests:** `test_search_input_is_escaped_in_output`,
`test_product_text_is_escaped`, `test_responses_carry_security_headers`.

## 3. Cross-site request forgery (CSRF)

**Risk:** another website tricks a logged-in admin's browser into sending a
request, such as deleting a product.

**What stops it:** every form includes `@csrf`, a secret token tied to the
session. Laravel rejects any POST, PUT, PATCH or DELETE without the right
token (error 419). Logout is a POST form too, so a link on another site
cannot log the admin out. Every change uses the right method: adding
is POST, saving is PUT, hide or show is PATCH, deleting is DELETE.

**Where:** `resources/views/admin/` (forms), `routes/web.php`.

**Tests:** `test_admin_forms_need_a_csrf_token`,
`test_logout_needs_a_post_request`.

## 4. Password hashing

**Risk:** if the database leaks, passwords are readable.

**What stops it:** the `hashed` cast on the User model hashes every
password with bcrypt before it is saved, so only the hash is stored.
Logging in compares against the hash. The password is also hidden when a
user is turned into JSON.

**Where:** `app/Models/User.php` (`casts()`, `$hidden`).

## 5. Input and form validation

**Risk:** bad or unexpected data, such as a negative price or a made-up
category, gets saved.

**What stops it:** every form goes through a Form Request class with rules
for each field (required, type, length, number range, category must
exist). Only validated fields are saved (`$request->safe()`). On the User
model `is_admin` is not fillable, so no request can make someone an admin.

**Where:** `app/Http/Requests/Admin/ProductRequest.php`,
`app/Http/Requests/Admin/LoginRequest.php`,
`app/Http/Requests/ProductListingRequest.php`.

**Tests:** `test_the_form_is_validated`, `test_extra_fields_are_ignored`,
`test_search_rejects_invalid_parameters`,
`test_the_visibility_change_is_validated`.

## 6. Image uploads

**Risk:** someone uploads a script (for example `shell.php` or an SVG with
JavaScript inside) instead of a picture, or a huge file.

**What stops it:**

- Only JPG, PNG and WebP are accepted, and Laravel checks the file's
  actual contents, not its name. SVG is refused because it can carry
  scripts.
- Files must be 2 MB or less and between 100 and 4000 pixels on each side.
- The file is saved under a new random name, and its extension comes from
  its contents, so the uploader controls neither the name nor the type.
- Old uploads are deleted when replaced, and the seeded sample pictures
  can never be deleted.

**Where:** `app/Http/Requests/Admin/ProductRequest.php` (`image` rule),
`app/Http/Controllers/Admin/ProductController.php` (`storeImage()`).

**Tests:** `test_an_uploaded_image_is_stored_under_a_random_name`,
`test_unsafe_or_oversized_files_are_refused`.

## 7. Login and admin authorisation

**Risk:** someone guesses the password, steals a session, or logs in with
an account that is not an admin.

**What stops it:**

- Five wrong passwords for the same email and IP lock that login for a
  minute, and the login form is also limited to 10 tries a minute per IP.
- The error message never says whether the email exists.
- A new session ID is issued on login (stops session fixation). Logout
  ends the session and replaces the CSRF token.
- After checking the password, the app checks the `is_admin` flag. An
  account without it is logged straight back out.

**Where:** `app/Http/Requests/Admin/LoginRequest.php`,
`app/Http/Controllers/Admin/LoginController.php`,
`app/Http/Middleware/EnsureUserIsAdmin.php`.

**Tests:** `test_login_locks_after_five_failed_attempts`,
`test_wrong_password_gives_a_generic_error`,
`test_an_admin_can_log_in_and_the_session_is_renewed`,
`test_non_admin_accounts_cannot_log_in`, `test_logout_ends_the_session`.

## 8. Unauthorised access to admin pages

**Risk:** someone types `/admin/products` into the address bar without
logging in.

**What stops it:** every admin route sits in one group protected by two
middleware: `auth` (must be logged in) and `admin` (must be an admin).
Guests are sent to the login page. The admin flag is checked on every
request, so removing it locks that person out straight away, not at their
next login. There is no sign-up page, so the only way to get an admin
account is the seeder.

On the storefront, hidden products return 404, exactly like products
that do not exist.

**Where:** `routes/web.php`, `bootstrap/app.php` (registers `admin`),
`app/Http/Middleware/EnsureUserIsAdmin.php`.

**Tests:** `test_guests_are_sent_to_the_login_page_from_every_admin_page`,
`test_guests_and_pending_users_cannot_change_products`,
`test_non_admin_accounts_with_a_session_are_turned_away`,
`test_a_removed_admin_loses_access_straight_away`,
`test_inactive_and_missing_products_return_404`.

## 9. Environment variables and credentials

**Risk:** database passwords, the app key or the admin password end up in
the GitHub repository.

**What stops it:**

- All secrets live in `.env`, which is listed in `.gitignore` and never
  committed. Only `.env.example`, with placeholder values, is in the repo.
- Code reads settings through `config/` files, never straight from
  `.env`.
- The admin account is created by `php artisan db:seed` from
  `ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env`. There is no password in
  the code. The seeder refuses passwords under 8 characters, and running it
  again never resets a changed password.

**Where:** `.gitignore`, `.env.example`, `config/admin.php`,
`database/seeders/AdminSeeder.php`.

**Tests:** `test_the_seeder_creates_the_admin_from_env_settings`,
`test_the_seeder_skips_missing_or_weak_settings`,
`test_seeding_again_never_resets_the_admin_password`.

## Extra browser protections

Every page also sends these headers (`app/Http/Middleware/SecurityHeaders.php`):

- `Content-Security-Policy`: scripts, styles and images only from trusted
  places, and forms can only post back to this site.
- `X-Frame-Options: DENY`: the site cannot be shown inside another
  site's frame (stops clickjacking).
- `X-Content-Type-Options: nosniff`: the browser will not guess a file's
  type, so an upload cannot be treated as a script.
- `Referrer-Policy` and `Permissions-Policy`: share less with other sites
  and turn off camera, microphone and location.

## Before going live

The settings in `.env.example` are for running locally. On a real server:

- Set `APP_ENV=production` and `APP_DEBUG=false`, so error pages never
  show code or settings.
- Generate a fresh `APP_KEY` with `php artisan key:generate`.
- Use a strong `ADMIN_PASSWORD` and a database user with its own password.
- Serve the site over HTTPS.
