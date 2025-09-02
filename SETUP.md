# Project Setup Instructions

This guide will walk you through the steps to clone and run this project locally.

## Prerequisites

Before you begin, ensure you have the following software installed on your machine:

- **PHP** (version `^8.2` or higher) - [Installation Guide](https://www.php.net/manual/en/install.php)
- **Composer** (PHP dependency manager) - [Installation Guide](https://getcomposer.org/doc/00-intro.md)
- **Node.js** and **npm** - [Installation Guide](https://nodejs.org/en/download/)
- A local database server (the project is configured to use SQLite by default, which is a file-based database and requires no separate server).

If you don't have a PHP development environment set up, you can use tools like [Laravel Herd](https://herd.laravel.com/) or [Laravel Sail](https://laravel.com/docs/sail) (which uses Docker) to get started quickly.

## 1. Clone the Repository

First, clone the project from the repository to your local machine:

```bash
git clone <repository-url>
cd <project-directory>
```

## 2. Set Up the Environment File

Copy the example environment file to create your own local environment configuration:

```bash
cp .env.example .env
```

## 3. Install Dependencies

Install the required PHP and JavaScript dependencies:

```bash
composer install
npm install
```

## 4. Generate Application Key

Generate a unique application key to secure your application:

```bash
php artisan key:generate
```

## 5. Set Up the Database

Run the database migrations and seed the database with initial data. This command will create all the necessary tables and populate them with the default data.

```bash
php artisan migrate:fresh --seed
```

## 6. Run the Application

Start the Laravel development server to run the application:

```bash
php artisan serve
```

If port 8000 is already in use, the server will automatically find the next available port (e.g., 8001). You can access the application by navigating to the URL provided in the terminal, such as `http://127.0.0.1:8001`.
