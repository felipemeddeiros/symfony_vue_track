# Symfony Backend - Track Management API

## 🚀 Quick Setup

1. **Install Dependencies:**
   ```bash
   composer install
   ```

2. **Configure Database:**
   ```bash
   cp .env .env.local
   # Edit .env.local with your MySQL credentials
   ```
   
   Example `.env.local`:
   ```env
   DATABASE_USER=root
   DATABASE_PASSWORD=root
   DATABASE_HOST=127.0.0.1
   DATABASE_PORT=3306
   DATABASE_NAME=symfony_vue_track
   ```

3. **Create Database & Run Migrations:**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

4. **Start Server:**
   ```bash
   symfony serve
   # Or: php -S localhost:8000 -t public
   ```

## 📡 API Endpoints

### List All Tracks
```http
GET /api/tracks
```

**Response (200):**
```json
[
  {
    "id": 1,
    "title": "Bohemian Rhapsody",
    "artist": "Queen",
    "duration": 354,
    "isrc": "GB-UM7-75-00123"
  }
]
```

### Create Track
```http
POST /api/tracks
Content-Type: application/json

{
  "title": "Bohemian Rhapsody",
  "artist": "Queen",
  "duration": 354,
  "isrc": "GB-UM7-75-00123"
}
```

**Response (201):**
```json
{
  "id": 1,
  "title": "Bohemian Rhapsody",
  "artist": "Queen",
  "duration": 354,
  "isrc": "GB-UM7-75-00123"
}
```

**Validation Error (422):**
```json
{
  "title": ["Title is required"],
  "duration": ["Duration must be a positive number"]
}
```

### Update Track
```http
PUT /api/tracks/{id}
Content-Type: application/json

{
  "title": "Bohemian Rhapsody (Remastered)",
  "artist": "Queen",
  "duration": 354,
  "isrc": "GB-UM7-75-00123"
}
```

**Response (200):**
```json
{
  "id": 1,
  "title": "Bohemian Rhapsody (Remastered)",
  "artist": "Queen",
  "duration": 354,
  "isrc": "GB-UM7-75-00123"
}
```

**Not Found (404):**
```json
{
  "error": "Resource not found"
}
```

## 🏗️ Architecture

### Request flow
1. **Controller** decodes JSON, builds a DTO via `CreateTrackDto::fromArray()` or `UpdateTrackDto::fromArray()`.
2. **ValidationService** validates the DTO with the Symfony Validator; on failure throws `ValidationException` (422 with field errors).
3. **TrackService** receives the validated DTO and persists (create or update). No validation in the service or entity.

### DTOs (validation lives here)
- **[`CreateTrackDto`](src/Dto/CreateTrackDto.php)** – Create payload. `final` class; properties use PHP 8.4 `private(set)` (asymmetric visibility). Validation: NotBlank (title, artist), Positive (duration), Regex (isrc when present). `fromArray()` + `normalizeRequiredString` / `normalizeOptionalString`.
- **[`UpdateTrackDto`](src/Dto/UpdateTrackDto.php)** – Update payload. All fields optional. Same constraints with `NotBlank(allowNull: true)`; Positive and Regex allow null. `fromArray()` uses `normalizeString()` for string fields.

### Service layer
- **[`TrackService`](src/Service/TrackService.php)** – Persistence only; accepts `CreateTrackDto` / `UpdateTrackDto`; no ValidationService dependency.
- **[`ValidationService`](src/Service/ValidationService.php)** – Validates any object (DTOs); throws `ValidationException` with field-level errors.

### Exception handling
- **[`ExceptionListener`](src/EventListener/ExceptionListener.php)** – Global exception handler (ValidationException → 422, NotFoundHttpException → 404).
- **[`ValidationException`](src/Exception/ValidationException.php)** – 422 response with validation errors map.

### Entity (persistence only)
- **[`Track`](src/Entity/Track.php)** – Doctrine entity. No validation attributes; PHP 8.4 `private(set)` on all properties; readable via `$track->title`, etc. Constructor and `update()` are the only writers.

## ✅ Validation Rules (on DTOs)

| Field | Create | Update | Constraints |
|-------|--------|--------|-------------|
| `title` | required | optional | NotBlank (Create: required; Update: allowNull: true) |
| `artist` | required | optional | NotBlank (Create: required; Update: allowNull: true) |
| `duration` | required | optional | Positive (seconds); null allowed on Update |
| `isrc` | optional | optional | Regex: `^[A-Z]{2}-[A-Z0-9]{3}-\d{2}-\d{5}$`; null/empty allowed |

### ISRC format
- Pattern: `XX-XXX-XX-XXXXX`
- Example: `US-ABC-12-34567`
- Optional (null or empty string normalized to null)


