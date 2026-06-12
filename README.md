# PerisaiFinance

## Overview

PerisaFinance is a professional financial management application built with Laravel 10 and Filament, designed to provide comprehensive financial tracking and insights.

## 🌟 Key Features

### Financial Management
- **Real-time Dashboard Analytics**: Gain instant insights into your financial performance
- **Advanced Category Management**: Organize transactions with precision
- **Comprehensive Transaction Tracking**: Detailed recording and management of financial activities

### Technical Highlights
- Modern Laravel 10 framework
- Filament Admin Panel integration
- Vite asset compilation
- Tailwind CSS for responsive design
- Robust authentication and authorization

## 🛠 Technology Stack

- **Backend**: Laravel 10
- **Admin Panel**: Filament 3.2
- **Frontend**: Tailwind CSS, Vite
- **Database**: MySQL/PostgreSQL
- **Language**: PHP 8.1+

## 📦 Prerequisites

Before installation, ensure you have:
- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL or PostgreSQL
- Laravel 10
- Filament 3.2

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/rhannprtmaa/PerisaiFinance.git
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install NPM packages
npm install
```

### 3. Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
```

### 4. Database Setup

```bash
# Run migrations
php artisan migrate
```

### 5. Start Development Server

```bash
php artisan serve
```

## 📂 Project Structure

```
finansphere/
│
├── app/            # Core application logic
├── bootstrap/      # Framework bootstrap files
├── config/         # Configuration files
├── database/       # Migrations and seeds
├── public/         # Public assets
├── resources/      # Views and frontend resources
├── routes/         # Application routes
├── storage/        # Application storage
├── tests/          # Test files
└── vendor/         # Composer dependencies
```

## 🔧 Admin Panel Features

- Dashboard with financial analytics
- Category management
- Comprehensive transaction tracking
- User and permission management

## 🛡 Best Practices

- Adheres to PSR coding standards
- Comprehensive type hinting
- Thin controllers with service-based architecture
- Thorough unit testing
- Detailed code documentation

## 🐞 Troubleshooting

### Common Solutions

- Clear application cache:
  ```bash
  php artisan cache:clear
  ```

- Check error logs:
  ```bash
  # Logs located at
  storage/logs/
  ```

- Resolve NPM issues:
  ```bash
  rm -rf node_modules
  npm install
  ```
  

## 🙌 Credits

- Developer: **Laode Raihan Pratama**
