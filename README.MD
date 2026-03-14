# Symfony + Vue.js Coding Test: Track Management Mini App

## ✨ Overview
A full-stack web application for managing music tracks, built with Symfony 6+ backend and Vue.js 3 frontend.

---

### Backend Setup
```bash
cd backend
composer install
cp .env .env.local
# Edit .env.local with your MySQL credentials
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony serve
# Or: php -S localhost:8000 -t public
```

### Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

**Access the application:**
- Frontend: http://localhost:5173
- Backend API: http://localhost:8000/api/tracks

**Important:** Use the committed `backend/.env` as the base config, then override local machine settings in `backend/.env.local` before running the database commands.

---

## 📋 Features Implemented

### Backend (Symfony 6+)
✅ **REST API Endpoints:**
- `GET /api/tracks` - List all tracks
- `POST /api/tracks` - Create a new track
- `PUT /api/tracks/{id}` - Update an existing track

✅ **Track Entity & DTOs:**
- **Track** entity: Doctrine-mapped, no validation rules; uses PHP 8.4 asymmetric visibility (`private(set)`) on properties; readable via property access (e.g. `$track->title`).
- **CreateTrackDto** / **UpdateTrackDto**: Validation lives on DTOs (NotBlank, Positive, Regex). Both use `private(set)` and static `fromArray()` with optional `normalizeRequiredString` / `normalizeOptionalString` helpers. Create is `final`.
- Flow: Controller → build DTO from request → ValidationService validates DTO → TrackService persists from validated DTO.

✅ **Additional Features:**
- Doctrine ORM for database management
- Service layer (TrackService for persistence; ValidationService for DTO validation)
- Validation on DTOs only; entity stays persistence-only
- CORS configuration for frontend access
- Database migrations
- Clean JSON response format
- PHP 8.4 (asymmetric property visibility)

### Frontend (Vue 3)
✅ **User Interface:**
- Track listing in responsive table format
- Create/edit track form with validation
- Real-time validation feedback
- Auto-refresh after create/update operations

✅ **Advanced Features:**
- Duration input in mm:ss format (converted to/from seconds)
- ISRC format validation
- Composition API with composables pattern
- Axios for API communication
- Modern, gradient-styled UI
- Error handling and display

---

## 💡 Key Design Decisions

1. **DTO-based validation & service layer:**
   - [`CreateTrackDto`](backend/src/Dto/CreateTrackDto.php) / [`UpdateTrackDto`](backend/src/Dto/UpdateTrackDto.php) hold validation rules and `fromArray()`; use PHP 8.4 `private(set)` so only the DTO can write properties.
   - Controller builds DTO from request, then [`ValidationService`](backend/src/Service/ValidationService.php) validates the DTO; [`TrackService`](backend/src/Service/TrackService.php) receives only validated DTOs and persists (no validation in service or entity).
   - [`Track`](backend/src/Entity/Track.php) entity has no validation attributes; it is persistence-only and also uses `private(set)` for asymmetric visibility.

2. **Global Exception Handling:**
   - [`ExceptionListener`](backend/src/EventListener/ExceptionListener.php) catches all exceptions
   - [`ValidationException`](backend/src/Exception/ValidationException.php) provides structured error responses
   - Consistent error format across all endpoints

3. **Composables Pattern:**
   - Used Vue 3 composables instead of Pinia for simpler state management
   - All API logic centralized in [`useTracks.js`](frontend/src/composables/useTracks.js)

4. **Duration Format:**
   - Stored as integer seconds in database
   - Displayed as mm:ss in UI for better UX
   - Auto-formatting and validation in [`TrackForm.vue`](frontend/src/components/TrackForm.vue)

5. **CORS Handling:**
   - Configured via [`nelmio_cors.yaml`](backend/config/packages/nelmio_cors.yaml)
   - Allows localhost:5173 for development

6. **Validation strategy:**
   - Validation lives on DTOs (PHP 8 attributes: NotBlank, Positive, Regex); entity has no constraints.
   - Controller validates DTO with ValidationService before calling TrackService; invalid requests return 422 with field-level errors.
   - Dual-layer validation (client + server); real-time client-side feedback.
