# Meeting Booking Service

Portfolio-oriented booking platform with `Laravel 11 + PHP 8.4 + Vue 3`.

The project demonstrates:

- REST API with JWT authentication
- booking CRUD with ownership rules
- subscription limits (`basic` and `premium`)
- Laravel events and listeners
- activity feed from booking events
- rate limiting and consistent API errors
- Vue SPA with Pinia, Vue Router and Axios
- Docker-based local setup

## Stack

- PHP 8.4
- Laravel 11
- MySQL 8.4
- tymon/jwt-auth
- Vue 3
- Vite
- Pinia
- Vue Router
- Axios
- Docker Compose
- PHPUnit

## Repository Layout

- `backend/` Laravel API
- `frontend/` Vue SPA
- `docker/` Dockerfiles and nginx config
- `docs/` architecture, API and database notes

## Architecture

Backend is intentionally split into clear layers:

- `app/Http` HTTP transport: controllers, requests, resources
- `app/Application` use cases / actions
- `app/Domain` contracts for business capabilities
- `app/Infrastructure` Eloquent-backed implementations
- `app/Events` and `app/Listeners` for event-driven behavior

This keeps controllers thin, business rules centralized and dependencies injected through contracts in `AppServiceProvider`.

## Features

- Register, login, logout and current-user profile via JWT
- Create, list, view, reschedule and cancel personal bookings
- Subscription summary endpoint with remaining slots
- Time conflict detection for overlapping meetings
- Plan-aware limits:
  - `basic`: up to 3 active future bookings
  - `premium`: up to 20 active future bookings
- Domain events:
  - `BookingCreated`
  - `BookingCancelled`
  - `BookingRescheduled`
- Activity logging to `booking_logs`
- Notification preparation stubs through listeners
- Rate limits:
  - `POST /api/auth/login` -> 5 req/min
  - `POST /api/auth/register` -> 3 req/min
  - booking mutations -> 20 req/min
  - global API -> 60 req/min

## Quick Start With Docker

```bash
cp .env.example .env
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
docker compose up --build -d
```

Services:

- API: `http://localhost:8080`
- Frontend: `http://localhost:5173`
- MySQL: `localhost:3306`

Demo users created by seeder:

- `basic@example.com` / `password123`
- `premium@example.com` / `password123`

## Local Run Without Docker

### Backend

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
```

### Frontend

```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

## Tests

Backend tests:

```bash
cd backend
php artisan test
```

Frontend production build:

```bash
cd frontend
npm run build
```

## API Overview

Auth:

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/me`

Bookings:

- `GET /api/bookings`
- `GET /api/bookings/{id}`
- `POST /api/bookings`
- `PUT /api/bookings/{id}`
- `DELETE /api/bookings/{id}`
- `GET /api/activity`

Subscription:

- `GET /api/subscription`

Detailed docs:

- [Architecture](docs/architecture.md)
- [API](docs/api.md)
- [ER Diagram](docs/er-diagram.md)
