# contacts-manager
Web application to manage your personal contacts with basic CRUD functionality with additional features like grouping and others


Here is the README in markdown format for you to copy:

````markdown
# Project README

## Introduction

Welcome to the Laravel 12 + Vue.js + Inertia.js project! This README will guide you through the necessary steps to set up the project on your local environment, explain essential dependencies, and provide basic usage instructions.

---

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Project Setup](#project-setup)
   - [Clone the Repository](#clone-the-repository)
   - [Install Dependencies](#install-dependencies)
3. [Environment Configuration](#environment-configuration)
   - [Create `.env` File](#create-env-file)
   - [Database Configuration](#database-configuration)
4. [Development Server](#development-server)
   - [Run Laravel Server](#run-laravel-server)
   - [Run Vue.js with Inertia](#run-vuejs-with-inertia)
   - [Use in GitHub Codespace](#use-in-github-codespace)
5. [Building for Production](#building-for-production)
6. [Testing](#testing)
7. [Contributing](#contributing)
8. [License](#license)

---

## System Requirements

To run this project locally, make sure you have the following software installed:

- **PHP** >= 8.2 except 8.3
- **Composer** >= 2.x
- **Node.js** >= 18.x
- **npm** >= 8.x
- **MySQL** >= 5.7

---

## Project Setup

### Clone the Repository

First, clone the repository to your local machine:

```bash
git clone https://github.com/JimmyKurui/contacts-manager.git
cd contacts-manager
````

### Install Dependencies

#### 1. Backend (Laravel)

Install the PHP dependencies using Composer:

```bash
composer install
```

#### 2. Frontend (Vue.js & Inertia.js)

Install the frontend dependencies using npm or yarn:

```bash
npm install
# or
yarn install
```

---

## Environment Configuration

### Create `.env` File

Copy the example `.env.example` file to create your own `.env` configuration file:

```bash
cp .env.example .env
```

Make sure to update your `.env` file with the correct environment settings.

### Database Configuration

Ensure your MySQL database configuration is set up properly in the `.env` file:

```plaintext
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

---

## Development Server

### Run Laravel Server

To run the Laravel backend:

```bash
php artisan serve
```

This will start the Laravel development server at `http://127.0.0.1:8000`.

### Run Vue.js with Inertia

To run the Vue.js frontend with Inertia.js:

```bash
npm run dev
# or
yarn dev
```

This will start the development server for Vue.js at `http://localhost:5173`.

### Use in GitHub Codespace

If you are using a GitHub Codespace for development, you can run the Vue.js development server with the following command instead:

```bash
npm run watch
```

This will start the Vue.js application in "watch mode", automatically rebuilding assets on changes.

---

## Building for Production

For production environments, you need to build the frontend assets. Run the following command to build the Vue.js assets:

```bash
npm run build
# or
yarn build
```

This will compile and minify the assets for production.

To deploy the Laravel application, make sure to set the correct environment settings, clear the cache, and migrate the database:

```bash
php artisan config:cache
php artisan route:cache
php artisan migrate --force
```

---

## Testing

Run your custom tests with Pest for the backend:

```bash
php artisan test
```

For frontend testing, you can use Jest or any testing library you’ve set up with Vue.js. If using Jest, run:

```bash
npm run test
# or
yarn test
```

---

## Contributing

We welcome contributions to the project! Please fork the repository, create a new branch, and submit a pull request with your changes.

1. Fork the repository
2. Create a feature branch (`git checkout -b feature-name`)
3. Commit your changes (`git commit -am 'Add feature'`)
4. Push to the branch (`git push origin feature-name`)
5. Submit a pull request

---
 
 