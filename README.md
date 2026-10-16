# Store

How to install, configure and run the Store application on your own computer.

## Step 1: Install the tools

You need four tools: **PHP** (8.2 or newer), **Composer**, **Node.js** (18 or newer) and **Git**. If you already have them, skip to [Step 2](#step-2-download-the-project).

To check what you already have, open a terminal and run:

```bash
php -v
composer --version
node -v
git --version
```

If a command prints a version number, that tool is installed. If it says "command not found" or "not recognized", install it using the instructions for your system below.

> **Opening a terminal:** on Windows, open the Start menu and search for **PowerShell**. On macOS, open **Terminal** from Applications > Utilities. On Linux, press `Ctrl+Alt+T`.

### Windows

1. **PHP and Composer:** open **PowerShell as administrator** (right-click PowerShell, then choose *Run as administrator*) and run:

   ```powershell
   Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.4'))
   ```

   This is Laravel's official installer. It installs PHP 8.4 and Composer.
2. **Node.js:** download the **LTS** installer from https://nodejs.org and run it. Keep the default options.
3. **Git:** download the installer from https://git-scm.com/download/win and run it. Keep the default options.
4. **Close and reopen PowerShell** so it picks up the new tools, then run the version checks above again.

### macOS

1. **PHP and Composer:** in Terminal, run:

   ```bash
   /bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.4)"
   ```

2. **Node.js:** download the **LTS** installer from https://nodejs.org and run it.
3. **Git:** run `xcode-select --install` and follow the prompts. If Git is already installed, this tells you so.
4. **Close and reopen Terminal**, then run the version checks above again.

### Linux (Ubuntu or Debian)

1. **PHP and Composer:**

   ```bash
   /bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"
   ```

2. **Node.js and Git:**

   ```bash
   sudo apt update
   sudo apt install -y nodejs npm git
   ```

   Then run `node -v`. If the version is below 18, install the LTS version from https://nodejs.org instead.
3. **Close and reopen the terminal**, then run the version checks above again.

## Step 2: Download the project

Choose the folder where you want the project to live, then run:

```bash
git clone https://github.com/athenkosi-nomavila/store.git
cd store
```

Run all the remaining commands from inside this `store` folder.

## Step 3: Install the project's packages

```bash
composer install
npm install
```

`composer install` downloads the PHP packages, and `npm install` downloads the packages used to build the styles. Each command can take a minute or two.

## Step 4: Configure

1. Create your settings file by copying the example:

   ```bash
   cp .env.example .env
   ```

   On Windows PowerShell, use `copy .env.example .env` instead.

2. Generate the app's secret key. It is written into `.env` automatically:

   ```bash
   php artisan key:generate
   ```

The default settings work on your own computer, so you don't need to change anything else. These are the settings in `.env` you might want to change later:

| Setting | Default | What it does |
| --- | --- | --- |
| `APP_NAME` | `Store` | The shop name shown on the site |
| `APP_URL` | `http://localhost` | The address the site runs on |
| `APP_TIMEZONE` | `Africa/Johannesburg` | The time zone used for dates |
| `APP_DEBUG` | `true` | Shows detailed error pages. Set it to `false` on a live server |

Keep `.env` private. It contains your secret key, and Git is already set up to ignore it.

## Step 5: Build the styles

```bash
npm run build
```

Run this again whenever you change the views or `resources/css/app.css`.

## Step 6: Run the application

```bash
php artisan serve
```

Open **http://127.0.0.1:8000** in your browser. Leave the terminal open while you use the site, and press `Ctrl+C` there to stop the server.

The next time you want to run the site, just open a terminal in the `store` folder and run `php artisan serve`.

### Optional: run the tests

```bash
php artisan test
```

All tests should pass.

## If something goes wrong

| Message or problem | What to do |
| --- | --- |
| `php`, `composer`, `node` or `git` is "not recognized" or "command not found" | Close and reopen the terminal. If it still fails, repeat that tool's install in Step 1. |
| `Your requirements could not be resolved` or a PHP extension is missing during `composer install` | Run `php -v` and check that it shows version 8.2 or newer. Then re-run the PHP installer from Step 1. |
| `No application encryption key has been specified` | Run `php artisan key:generate`. |
| `Vite manifest not found` | Run `npm run build`. |
| The page shows but has no styling | Run `npm run build`, then refresh the browser with `Ctrl+Shift+R` (`Cmd+Shift+R` on macOS). |
| `Failed to listen on 127.0.0.1:8000` or `Address already in use` | Something else is using that port. Run `php artisan serve --port=8001` and open http://127.0.0.1:8001. |
