# Task Management API

A RESTful API built with Laravel for managing tasks and categories with authentication.

## Features

- User Authentication (Register, Login, Logout)
- Task Management (CRUD operations)
- Category Management (CRUD operations)
- Advanced Task Filtering
- Token-based Authentication using Sanctum

## Requirements

- PHP 8.1+
- Laravel 10.x
- MySQL
- Composer

## Installation

1. Clone the repository
```bash
git clone https://github.com/alkasima/task-management.git
cd task-management
```

2. Install dependencies
```bash
composer install
```

3. Copy environment file
```bash
cp .env.example .env
```

4. Configure your database in .env file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Generate application key
```bash
php artisan key:generate
```

6. Run migrations
```bash
php artisan migrate
```

7. Start the server
```bash
php artisan serve
```

## API Endpoints

### Authentication

```
POST /api/register - Register a new user
POST /api/login - Login user
POST /api/logout - Logout user
```

### Categories

```
GET /api/categories - List all categories
POST /api/categories - Create a category
GET /api/categories/{id} - Get a category
PUT /api/categories/{id} - Update a category
DELETE /api/categories/{id} - Delete a category
```

### Tasks

```
GET /api/tasks - List all tasks (with filters)
POST /api/tasks - Create a task
GET /api/tasks/{id} - Get a task
PUT /api/tasks/{id} - Update a task
DELETE /api/tasks/{id} - Delete a task
```

## Testing

Run the test suite:
```bash
php artisan test
```
## API Documentantion (Postman)
https://documenter.getpostman.com/view/18112964/2sAYBXBqxJ

## License

The MIT License (MIT)
