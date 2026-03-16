# Architecture

## Goals

- keep controllers transport-only
- isolate use cases from persistence details
- centralize business rules
- make dependencies explicit through interfaces and DI

## Backend Layers

### `app/Http`

- `Controllers`: HTTP orchestration only
- `Requests`: validation rules
- `Resources`: response shaping

### `app/Application`

- use-case level actions such as:
  - register user
  - create booking
  - reschedule booking
  - cancel booking
  - calculate subscription summary

### `app/Domain`

- contracts for booking retrieval, conflict detection and subscription quota resolution
- domain-specific exceptions for business conflicts and plan limits

### `app/Infrastructure`

- Eloquent-backed implementations of domain contracts
- persistence details stay here instead of leaking into controllers

### Events

- `BookingCreated`
- `BookingCancelled`
- `BookingRescheduled`

Each event fans out into:

- activity log persistence
- notification preparation stub via Laravel log

## Frontend Structure

- `src/api`: Axios client
- `src/router`: route definitions and guards
- `src/stores`: Pinia stores for auth and bookings
- `src/components`: reusable UI blocks
- `src/views`: page-level screens
- `src/utils`: date transformation helpers

## SOLID / DRY / KISS Notes

- `S`: controllers do one thing, actions do one use case, repositories do persistence
- `O`: new persistence strategy can replace current Eloquent adapters via contracts
- `L`: contracts are simple and substitutable
- `I`: small interfaces per concern instead of one large gateway
- `D`: application layer depends on contracts, not concrete Eloquent implementations
- `DRY`: conflict and quota logic are shared through dedicated services/contracts
- `KISS`: no unnecessary CQRS/event-sourcing overhead; plain Laravel where it stays readable
