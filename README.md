# Store

A small online shop built with Laravel 11 and Tailwind CSS. This version contains the customer-facing storefront: a homepage, product pages, category pages and search. Products currently come from a sample catalogue file, so no database is needed yet.

![Homepage on desktop](docs/screenshots/home-desktop.png)

## 1. Requirements

Install these before you start. The commands in brackets show how to check each one.

| Tool | Version | Check |
| --- | --- | --- |
| PHP | 8.2 or newer, with the `mbstring`, `openssl` and `fileinfo` extensions | `php -v` |
| Composer | 2.x | `composer --version` |
| Node.js and npm | Node 18 or newer | `node -v` and `npm -v` |
| Git | any recent version | `git --version` |

## 2. Install

Run these commands in a terminal, one after another.

```bash
# Download the code
git clone https://github.com/athenkosi-nomavila/store.git
cd store

# Install the PHP packages
composer install

# Install the packages used to build the styles
npm install
```

## 3. Configure

Create your own settings file from the example and give the app a secret key:

```bash
cp .env.example .env
php artisan key:generate
```

On Windows Command Prompt, use `copy .env.example .env` instead of `cp`.

The defaults in `.env` work as they are for running the site on your own computer. These are the settings you might change:

| Setting | Default | What it does |
| --- | --- | --- |
| `APP_NAME` | `Store` | The shop name shown in the header, footer and browser tab |
| `APP_ENV` | `local` | Set to `production` on a live server |
| `APP_DEBUG` | `true` | Shows detailed error pages. **Set to `false` on a live server** |
| `APP_URL` | `http://localhost` | The address the site is served from |
| `APP_TIMEZONE` | `Africa/Johannesburg` | Time zone for dates |

The `DB_` settings are not used yet. They are there for when the database is added.

**Never commit `.env`.** It holds your app key and, later, your database password. Git already ignores it.

### Changing the products

The sample products and categories are in `config/catalog.php`. Product images go in `public/images/products/`. Each product lists its image by file name, for example `'image' => 'speaker.jpg'`. If you edit this file while `config:cache` is active, run `php artisan config:clear` to see the change.

## 4. Run

Build the styles, then start the server:

```bash
npm run build
php artisan serve
```

Open **http://127.0.0.1:8000** in your browser. Press `Ctrl+C` in the terminal to stop the server.

### Working on the styles

If you are changing the look of the site, run the style watcher in a second terminal instead of `npm run build`:

```bash
npm run dev
```

The page then updates each time you save a view or `resources/css/app.css`. Stop it with `Ctrl+C` and run `npm run build` again when you are done.

### Viewing the site on your phone

Start the server so other devices on your Wi-Fi can reach it:

```bash
php artisan serve --host=0.0.0.0
```

Then open `http://<your computer's IP address>:8000` on the phone.

## 5. Run the tests

```bash
php artisan test
```

All tests should pass. They do not need a database or a build of the styles.

## Troubleshooting

| Problem | Fix |
| --- | --- |
| `Vite manifest not found` | Run `npm run build` (or keep `npm run dev` running). |
| `No application encryption key has been specified` | Run `php artisan key:generate`. |
| The page has no styling | Run `npm run build`. Then refresh with `Ctrl+Shift+R`. |
| `Address already in use` | Another program is using port 8000. Run `php artisan serve --port=8001` and open that port instead. |
| Changes to `config/catalog.php` don't show | Run `php artisan config:clear`. |
| `composer install` reports a missing PHP extension | Enable that extension in your `php.ini`, then run `composer install` again. |

## Pages

| Address | What it shows |
| --- | --- |
| `/` | Promotional banner, categories and the 8 newest products |
| `/shop` | All products, with a category filter, sorting and 12 products per page |
| `/search?q=...` | Products whose name or description contains the search term |
| `/category/{slug}` | Products in one category |
| `/products/{slug}` | One product's details and stock status, plus more from the same category |

## Security

- **XSS:** all output is escaped with Blade's `{{ }}`, including the search term. A Content Security Policy blocks inline and third-party scripts.
- **Input validation:** search terms are limited to 100 characters, sort must be a known option, and the category must exist.
- **Hidden products:** inactive products return a 404, the same as missing ones.
- **Security headers:** every page sends `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` and `Permissions-Policy`.
- **Secrets:** they live only in `.env`, which is never committed.
