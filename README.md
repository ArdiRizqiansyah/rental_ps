# Rental PS (Initialize Name)

This is a repository for Rental PS

## Dependencies

-   [Laravel v12](https://laravel.com/)
-   [Bootstrap 5.x](https://getbootstrap.com/)

## Requirements

-   PHP ^v8.1
-   Node v18

# Installation

Please follow below instructions for install the project.

### 1. Clone Project

Clone repository using this command:

```bash
git clone https://github.com/ArdiRizqiansyah/rental_ps.git
```

Access to your project

```bash
cd rental-ps
```

Create a new branch locally for pulling branch **dev**:

```bash
git checkout -b {your_branch}
```

Pull branch **dev** from remote:

```bash
git pull origin dev
```

### 2. Install Dependencies

Install all of dependencies with composer:

```bash
composer install
```

### 3. Setup Environment

Create your .env file by copy the .env.example with command:

```bash
cp .env.example .env
```

After doing that, you can setup your environment database connection like so:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={your_database}
DB_USERNAME={your_username}
DB_PASSWORD={your_password}

FILESYSTEM_DISK=public

MIDTRANS_SERVERKEY="<your_serverkey>"
MIDTRANS_CLIENTKEY="<your_clientkey>"
MIDTRANS_IS_PRODUCTION=false
```

Generate APP_KEY by using this command:

```bash
php artisan key:generate
```

### 5. Migrate & Seeder

Create all tables by using this command:

```bash
php artisan migrate
```

Import some data with seeder:

```bash
php artisan db:seed
```

### 5. Generate Autoload

Run this command for generate autoloader

```bash
composer dump-autoload
```

### 6. Create Symbolic Link for Storage

Run this command for generate symlink for storage:

```bash
php artisan storage:link
```

### 7. Install NPM (Node Package Manager)

Install NPM by using this command:

```bash
npm install
```

Compile NPM with this command:

```bash
npm run dev
```

### 8. Run Your Project

Run your project with this command:

```bash
php artisan serve
```

and access in browser

```bash
localhost:8000
```