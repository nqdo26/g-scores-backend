# G-Scores Backend API (Laravel)

Backend RESTful API for G-Scores - High School Exam Score Management System, built with Laravel 11.

## 📋 Project Overview

This API provides endpoints for managing and querying Vietnamese high school examination scores (THPT 2024), including:

-   Individual score lookup by registration number (SBD)
-   Statistical analysis by subject
-   Score distribution reports
-   Top 10 rankings for Group A subjects (Math, Physics, Chemistry)

**Dataset:** 1,061,605 student records from THPT 2024 examination.

## 🛠 Tech Stack

-   **Framework:** Laravel 11.x
-   **Language:** PHP 8.2+
-   **Database:** SQLite (development) / MySQL or PostgreSQL (production)
-   **Architecture:** MVC with Service Layer Pattern
-   **ORM:** Eloquent

## 📦 Requirements

-   PHP >= 8.2
-   Composer
-   SQLite (included with PHP)
-   Git

## 🚀 Installation & Setup

### 1. Clone the repository

```bash
git clone <repository-url>
cd g-scores-backend
```

### 2. Install dependencies

```bash
composer install
```

### 3. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Prepare data

```bash
# Ensure CSV file exists in data/ folder
# File: data/diem_thi_thpt_2024.csv (1,061,605 records)
```

### 5. Database setup

```bash
# Run migrations to create tables
php artisan migrate

# Import CSV data into database (~2-3 minutes)
php artisan db:seed --class=StudentSeeder
```

### 6. Start development server

```bash
php artisan serve
```

API will be available at: **http://localhost:8000**

## 📡 API Documentation

### Base URL

```
http://localhost:8000/api
```

### Endpoints

#### 1. Health Check

```http
GET /api/health
```

**Response:**

```json
{
    "success": true,
    "message": "API is running",
    "timestamp": "2024-12-27T13:15:00.000000Z"
}
```

#### 2. Check Score by Registration Number

```http
GET /api/scores/check/{sbd}
```

**Parameters:**

-   `sbd` (string, required): Student registration number (e.g., "01000001")

**Example Request:**

```bash
curl http://localhost:8000/api/scores/check/01000001
```

**Response:**

```json
{
    "success": true,
    "data": {
        "sbd": "01000001",
        "scores": {
            "toan": 8.4,
            "ngu_van": 6.75,
            "ngoai_ngu": 8.0,
            "vat_li": 6.0,
            "hoa_hoc": 5.25,
            "sinh_hoc": 5.0,
            "lich_su": null,
            "dia_li": null,
            "gdcd": null,
            "ma_ngoai_ngu": "N1"
        },
        "groupA": {
            "total": 19.65,
            "subjects": {
                "toan": 8.4,
                "vat_li": 6.0,
                "hoa_hoc": 5.25
            }
        }
    }
}
```

#### 3. Score Report by Levels

```http
GET /api/scores/report/{subject}
```

**Parameters:**

-   `subject` (string, required): Subject code (see valid subjects below)

**Response:**

```json
{
    "success": true,
    "data": {
        "subject": "Toán",
        "levels": {
            "excellent": {
                "count": 156234,
                "percentage": "14.72%"
            },
            "good": {
                "count": 298456,
                "percentage": "28.11%"
            },
            "average": {
                "count": 412389,
                "percentage": "38.84%"
            },
            "poor": {
                "count": 194526,
                "percentage": "18.33%"
            }
        },
        "total": 1061605
    }
}
```

#### 4. Subject Statistics

```http
GET /api/scores/statistics/{subject}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "subject": "Toán",
        "total": 1061605,
        "average": 5.67,
        "highest": 10.0,
        "lowest": 0.0,
        "median": 5.8,
        "distribution": {
            "excellent": 156234,
            "good": 298456,
            "average": 412389,
            "poor": 194526
        }
    }
}
```

#### 5. Top 10 Group A Students

```http
GET /api/scores/top10/group-a
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "rank": 1,
            "sbd": "26020938",
            "total": 29.6,
            "scores": {
                "toan": 9.8,
                "vat_li": 10.0,
                "hoa_hoc": 9.8
            }
        }
        // ... 9 more students
    ]
}
```

### Valid Subject Codes

| Code        | Subject          | Vietnamese Name |
| ----------- | ---------------- | --------------- |
| `toan`      | Mathematics      | Toán            |
| `ngu_van`   | Literature       | Ngữ Văn         |
| `ngoai_ngu` | Foreign Language | Ngoại Ngữ       |
| `vat_li`    | Physics          | Vật Lý          |
| `hoa_hoc`   | Chemistry        | Hóa Học         |
| `sinh_hoc`  | Biology          | Sinh Học        |
| `lich_su`   | History          | Lịch Sử         |
| `dia_li`    | Geography        | Địa Lý          |
| `gdcd`      | Civic Education  | GDCD            |

## 🏗 Project Structure

```
app/
├── Http/
│   └── Controllers/
│       └── ScoreController.php      # API endpoint handlers
├── Models/
│   └── Student.php                  # Eloquent model with accessors
└── Services/
    └── ScoreService.php             # Business logic layer

database/
├── migrations/
│   └── 2024_12_27_000001_create_students_table.php
└── seeders/
    └── StudentSeeder.php            # CSV data importer

routes/
└── api.php                          # API route definitions

config/
├── cors.php                         # CORS configuration
└── database.php                     # Database connections
```

## 🔧 Configuration

### Database

Default: SQLite (development)

```env
DB_CONNECTION=sqlite
```

For production (MySQL):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=g_scores
DB_USERNAME=root
DB_PASSWORD=your_password
```

### CORS

Configured for frontend at:

-   `http://localhost:3000`
-   `http://localhost:3001`

Edit `config/cors.php` to add more origins.

## 🧪 Testing

Run tests with PHPUnit:

```bash
php artisan test
```

## 📊 Database Schema

### `students` table

| Column       | Type         | Description                           |
| ------------ | ------------ | ------------------------------------- |
| id           | bigint       | Primary key                           |
| sbd          | string       | Registration number (unique, indexed) |
| toan         | decimal(3,1) | Math score                            |
| ngu_van      | decimal(3,1) | Literature score                      |
| ngoai_ngu    | decimal(3,1) | Foreign language score                |
| vat_li       | decimal(3,1) | Physics score                         |
| hoa_hoc      | decimal(3,1) | Chemistry score                       |
| sinh_hoc     | decimal(3,1) | Biology score                         |
| lich_su      | decimal(3,1) | History score                         |
| dia_li       | decimal(3,1) | Geography score                       |
| gdcd         | decimal(3,1) | Civic education score                 |
| ma_ngoai_ngu | string       | Foreign language code                 |
| created_at   | timestamp    | Created timestamp                     |
| updated_at   | timestamp    | Updated timestamp                     |

**Indexes:** `sbd`, `toan`, `vat_li`, `hoa_hoc` for optimized queries.

## 🎯 Key Features

1. **Service Layer Pattern**: Business logic separated from controllers
2. **Database Optimization**: Strategic indexes for performance
3. **Batch Processing**: Efficient CSV import with batch inserts (1000 records/batch)
4. **RESTful Design**: Standard HTTP methods and status codes
5. **Error Handling**: Comprehensive error messages and validation
6. **CORS Support**: Configured for frontend integration

## 🚀 Performance

-   **Dataset:** 1,061,605 records
-   **Query Performance:** < 100ms for single lookups
-   **Aggregation Queries:** < 500ms for statistics
-   **Top 10 Query:** < 200ms with optimized indexes

## 📝 Notes

-   SQLite is used for development for portability and ease of setup
-   For production deployment, PostgreSQL or MySQL is recommended
-   CSV file should be placed in `data/` folder before seeding
-   All scores are stored as decimal(3,1) for precision

## 📄 License

MIT

---

**API Status:** ✅ Operational  
**Last Updated:** December 27, 2025
