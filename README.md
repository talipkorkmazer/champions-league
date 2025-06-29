# Champions League Project

A Laravel-based application for managing and simulating football leagues with team statistics, match predictions, and automated simulations.

## Features

- Create and manage football leagues
- Team management and statistics
- Automated match simulation
- League standings and predictions
- Real-time match updates
- Modern Vue.js frontend with Inertia.js
- Responsive design with Tailwind CSS

## Local Development Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- Docker and Docker Compose

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd champions-league-project
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

5. **Start MySQL service with Docker Compose**
   ```bash
   docker-compose up -d
   ```

6. **Generate application key**
   ```bash
   php artisan key:generate
   ```

7. **Run database migrations and seed**
   ```bash
   php artisan migrate --seed
   ```

8. **Build assets**
   ```bash
   npm run build
   ```

### Running the Application

**For development with hot reload:**
```bash
composer run dev
```

This will start both the Laravel development server and Vite for asset compilation.

**Alternative: Run servers separately**
```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite dev server
npm run dev
```

### Access the Application

Open your browser and navigate to: `http://localhost:8000`

## Environment Variables

Configure the following key variables in your `.env` file:

```env
APP_NAME="Champions League Project"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=champions_league
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

## Available Commands

- `composer run dev` - Start development servers
- `php artisan serve` - Start Laravel development server
- `npm run dev` - Start Vite development server
- `npm run build` - Build production assets
- `php artisan migrate --seed` - Run database migrations and seed data
- `php artisan test` - Run tests 