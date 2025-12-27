# Quick Setup Guide

## Prerequisites
- PHP 8.2 or higher
- Composer installed
- Git

## Setup in 5 Steps

```bash
# 1. Install dependencies
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Run migrations
php artisan migrate

# 4. Import data (takes ~2-3 minutes for 1M+ records)
php artisan db:seed --class=StudentSeeder

# 5. Start server
php artisan serve
```

## Access API
- Base URL: http://localhost:8000/api
- Health check: http://localhost:8000/api/health
- Example: http://localhost:8000/api/scores/check/01000001

## Note
- CSV file `data/diem_thi_thpt_2024.csv` is required for seeding
- SQLite database will be created automatically in `database/database.sqlite`
- See [README.md](README.md) for full API documentation
