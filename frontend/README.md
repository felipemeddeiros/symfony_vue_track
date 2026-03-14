# Vue.js Frontend - Track Management UI

## 🚀 Quick Setup

1. **Install Dependencies:**
   ```bash
   npm install
   ```

2. **Start Development Server:**
   ```bash
   npm run dev
   ```

3. **Access Application:**
   - Frontend: http://localhost:5173
   - Ensure backend is running at http://localhost:8000

## ✨ Features

### Track Management
- ✅ **List Tracks** - Display all tracks in a responsive table
- ✅ **Create Track** - Add new tracks via form
- ✅ **Edit Track** - Update existing tracks by clicking edit; opens a **modal** with the form pre-filled
- ✅ **Real-time Validation** - Client-side validation with instant feedback
- ✅ **Server Error Display** - Show backend validation errors

### User Experience
- ✅ **Duration Format** - Input/display as mm:ss (e.g., 03:45)
- ✅ **Auto-formatting** - Duration auto-formats on blur
- ✅ **Form Reset** - Clears after successful submission
- ✅ **Edit in Modal** - Edit opens a modal with the form pre-filled with the track data
- ✅ **Cancel Option** - Close modal or return to create mode from edit
- ✅ **Responsive Design** - Works on mobile and desktop

## 🏗️ Architecture

### Component Structure

```
src/
├── components/
│   ├── TrackForm.vue      # Create/edit form with validation
│   ├── TrackList.vue      # Track table display
│   └── Modal.vue          # Reusable modal (used for edit track form)
├── composables/
│   └── useTracks.js       # State management & API logic
├── services/
│   └── api.js             # Axios HTTP client
├── App.vue                # Main application component
└── main.js                # Application entry point
```

### API Configuration
Base URL: `http://localhost:8000/api`

## 📝 Form Validation

### Client-Side Rules

| Field | Validation | Error Message |
|-------|-----------|---------------|
| Title | Required | "Title is required" |
| Artist | Required | "Artist is required" |
| Duration | Required, > 0 | "Duration is required and must be greater than 0" |
| ISRC | Optional, Regex | "ISRC must match format XX-XXX-XX-XXXXX" |

### Duration Format
- **Input:** mm:ss (e.g., 03:45)
- **Storage:** Integer seconds (e.g., 225)
- **Display:** mm:ss (e.g., 03:45)
- **Auto-formatting:** Adds colon after 2 digits
- **Validation:** Max 59:59

### ISRC Format
- **Pattern:** `[A-Z]{2}-[A-Z0-9]{3}-[0-9]{2}-[0-9]{5}`
- **Example:** `US-ABC-12-34567`
- **Optional:** Can be left empty
