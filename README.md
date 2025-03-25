# Quotes API

A PHP OOP REST API for managing quotes, authors, and categories. This API supports CRUD operations for quotes and allows users to retrieve quotes by various filters including author, category, and even a random quote feature.

## Project Overview

This project meets the INF653 Back End Web Development midterm requirements. It provides endpoints to:
- **GET** quotes, authors, and categories.
- **POST** new quotes, authors, and categories.
- **PUT** updates to quotes, authors, and categories.
- **DELETE** existing quotes, authors, and categories.

Each quote record includes a quote text, an author, and a category.

## Setup and Installation

### Requirements

- PHP 7.x or later
- MySQL Database
- A hosting service (e.g., 000webhost for deployment)

### Database Setup

1. Create a MySQL database named `quotesdb` (or as per your hosting provider’s naming).
2. Create the following tables with these columns:
   - **quotes:** `id` (auto-increment, primary key), `quote` (TEXT, NOT NULL), `author_id` (INT, NOT NULL), `category_id` (INT, NOT NULL)
   - **authors:** `id` (auto-increment, primary key), `author` (VARCHAR, NOT NULL)
   - **categories:** `id` (auto-increment, primary key), `category` (VARCHAR, NOT NULL)
3. Import the provided SQL schema and data into your database.

### Configuration

Edit `config/Database.php` to update the database connection details:
```php
private $host = "localhost";
private $db_name = "your_database_name";
private $username = "your_database_username";
private $password = "your_database_password";
