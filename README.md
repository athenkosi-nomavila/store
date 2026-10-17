# Store

How to install, configure and run the Store application on your own computer.

## Step 1: Install the tools

You need five tools: **PHP** (8.2 or newer), **Composer**, **Node.js** (18 or newer), **Git** and **MySQL** (8.0 or newer). If you already have them, skip to [Step 2](#step-2-download-the-project).

To check what you already have, open a terminal and run:

```bash
php -v
composer --version
node -v
git --version
mysql --version
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
4. **MySQL:** download the **MySQL Installer** from https://dev.mysql.com/downloads/installer/ and run it. Choose the *Server only* setup, and when it asks, pick a password for the `root` user and write it down.
5. **Close and reopen PowerShell** so it picks up the new tools, then run the version checks above again.

### macOS

1. **PHP and Composer:** in Terminal, run:

   ```bash
   /bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.4)"
   ```

   This puts `php` and `composer` in `~/.config/herd-lite/bin` and adds that folder to your `PATH` in `~/.zshrc`. It doesn't need Homebrew or Xcode. Use it instead of `brew install php`, which can fail on Macs with an older Xcode.

2. **Node.js:** download the **LTS** installer from https://nodejs.org and run it.
3. **Git:** run `xcode-select --install` and follow the prompts. If Git is already installed, this tells you so.
4. **MySQL:** download the **macOS DMG Archive** from https://dev.mysql.com/downloads/mysql/ (choose *ARM* for Apple silicon Macs, *x86* for Intel Macs) and run it. When it asks, pick a password for the `root` user and write it down. The `mysql` command is installed at `/usr/local/mysql/bin/mysql`.
5. **Close and reopen Terminal**, then run the version checks above again.

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
3. **MySQL:**

   ```bash
   sudo apt install -y mysql-server
   ```

   On Debian, use `sudo apt install -y mariadb-server` instead. It works the same way.
4. **Close and reopen the terminal**, then run the version checks above again.

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

3. Create the database and a user for the app. Open the MySQL prompt:

   - **Windows:** open **MySQL Command Line Client** from the Start menu and type your `root` password.
   - **macOS:** run `/usr/local/mysql/bin/mysql -u root -p` and type your `root` password.
   - **Linux:** run `sudo mysql`.

   Then paste these lines, replacing `choose-a-password` with a password of your own:

   ```sql
   CREATE DATABASE store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'store'@'localhost' IDENTIFIED BY 'choose-a-password';
   GRANT ALL PRIVILEGES ON store.* TO 'store'@'localhost';
   EXIT;
   ```

4. Open `.env` in a text editor and put the same password after `DB_PASSWORD=`:

   ```
   DB_PASSWORD=choose-a-password
   ```

5. Still in `.env`, choose the email and password you will use to log in to the admin (the password needs at least 8 characters):

   ```
   ADMIN_EMAIL=you@example.com
   ADMIN_PASSWORD=choose-an-admin-password
   ```

6. Create the tables, your admin account and the sample products:

   ```bash
   php artisan migrate --seed
   ```

   To wipe the database and start again with the sample products, run `php artisan migrate:fresh --seed`.

The other default settings work on your own computer. These are the settings in `.env` you might want to change later:

| Setting | Default | What it does |
| --- | --- | --- |
| `APP_NAME` | `Store` | The shop name shown on the site |
| `APP_URL` | `http://localhost` | The address the site runs on |
| `APP_TIMEZONE` | `Africa/Johannesburg` | The time zone used for dates |
| `APP_DEBUG` | `true` | Shows detailed error pages. Set it to `false` on a live server |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | `store`, `store`, empty | The MySQL database the app uses, and the user and password it logs in with |
| `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` | `Store Admin`, empty, empty | The first admin login, created by `php artisan migrate --seed`. Changing them later does not change an existing account |

Keep `.env` private. It contains your secret key, database password and admin password, and Git is already set up to ignore it.

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

### Log in to the admin

Open **http://127.0.0.1:8000/admin** and log in with the `ADMIN_EMAIL` and `ADMIN_PASSWORD` you put in `.env`.

The next time you want to run the site, just open a terminal in the `store` folder and run `php artisan serve`.

### Optional: run the tests

```bash
php artisan test
```

All tests should pass. The tests use a temporary in-memory database, so they never touch your MySQL data.

## If something goes wrong

| Message or problem | What to do |
| --- | --- |
| `php`, `composer`, `node` or `git` is "not recognized" or "command not found" | Close and reopen the terminal. If it still fails, repeat that tool's install in Step 1. On macOS you can also run `source ~/.zshrc` instead of reopening the terminal. |
| `brew install php` fails with `No available formula with the name "apr-util"` or a "Tier 3 configuration" warning | Homebrew doesn't support your setup, usually because Xcode is out of date. Use the php.new installer from Step 1 instead. `brew autoremove` removes the packages Homebrew left half-installed. |
| `Your requirements could not be resolved` or a PHP extension is missing during `composer install` | Run `php -v` and check that it shows version 8.2 or newer. Then re-run the PHP installer from Step 1. |
| `SQLSTATE[HY000] [1045] Access denied for user 'store'@'localhost'` | The password in `.env` doesn't match the one you chose in Step 4. Fix `DB_PASSWORD` and try again. |
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL isn't running. On Windows, start it from the Services app. On macOS, start it in System Settings > MySQL. On Linux, run `sudo service mysql start`. |
| `could not find driver` | Your PHP is missing the MySQL extension. Re-run the PHP installer from Step 1. |
| `No application encryption key has been specified` | Run `php artisan key:generate`. |
| `Vite manifest not found` | Run `npm run build`. |
| The page shows but has no styling | Run `npm run build`, then refresh the browser with `Ctrl+Shift+R` (`Cmd+Shift+R` on macOS). |
| `Failed to listen on 127.0.0.1:8000` or `Address already in use` | Something else is using that port. Run `php artisan serve --port=8001` and open http://127.0.0.1:8001. |
