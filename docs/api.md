# API Documentation

Base URL: `http://localhost:8080/api`

All protected routes require header:

```http
Authorization: Bearer <jwt-token>
Accept: application/json
```

## Error Format

```json
{
  "message": "Validation failed",
  "errors": {
    "starts_at": [
      "The starts_at field is required."
    ]
  }
}
```

## Auth

### `POST /auth/register`

Request:

```json
{
  "name": "Mikhail",
  "email": "mikhail@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

Response:

```json
{
  "message": "User registered successfully",
  "token": "jwt_token_here",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "Mikhail",
    "email": "mikhail@example.com",
    "subscription": {
      "id": 1,
      "code": "basic",
      "name": "Basic",
      "booking_limit": 3
    }
  }
}
```

### `POST /auth/login`

Request:

```json
{
  "email": "mikhail@example.com",
  "password": "secret123"
}
```

### `POST /auth/logout`

Protected route. Invalidates current token.

### `GET /auth/me`

Protected route. Returns current user with subscription.

## Bookings

### `GET /bookings`

Query params:

- `status`
- `date_from`
- `date_to`

### `POST /bookings`

Request:

```json
{
  "title": "Meeting with client",
  "description": "Project discussion",
  "starts_at": "2026-03-20 14:00:00",
  "ends_at": "2026-03-20 15:00:00"
}
```

Possible business errors:

- `409 Subscription limit exceeded`
- `409 Booking time conflicts with an existing meeting`

### `GET /bookings/{id}`

Protected route. Returns only owner booking.

### `PUT /bookings/{id}`

Request:

```json
{
  "starts_at": "2026-03-20 16:00:00",
  "ends_at": "2026-03-20 17:00:00"
}
```

### `DELETE /bookings/{id}`

Changes status to `cancelled`, sets `cancelled_at` and dispatches `BookingCancelled`.

## Subscription

### `GET /subscription`

Response:

```json
{
  "subscription": {
    "id": 1,
    "code": "basic",
    "name": "Basic",
    "booking_limit": 3
  },
  "active_bookings_count": 2,
  "remaining_slots": 1
}
```

## Rate Limits

- `POST /auth/login` -> 5 req/min
- `POST /auth/register` -> 3 req/min
- booking mutations -> 20 req/min per user
- global API -> 60 req/min

## Activity Feed

### `GET /activity`

Returns the latest booking-related events for the current user.

Response:

```json
{
  "data": [
    {
      "id": 10,
      "event_type": "booking.rescheduled",
      "old_value": {
        "starts_at": "2026-03-20T14:00:00+00:00"
      },
      "new_value": {
        "starts_at": "2026-03-20T16:00:00+00:00"
      },
      "created_at": "2026-03-18T09:10:00+00:00",
      "booking": {
        "id": 4,
        "title": "Meeting with client",
        "status": "active"
      }
    }
  ]
}
```
