<template>
  <div class="track-form">
    <h2>{{ isEditing ? 'Edit Track' : 'Create New Track' }}</h2>
    
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="title">Title *</label>
        <input
          id="title"
          v-model="formData.title"
          type="text"
          placeholder="Enter track title"
          :class="{ 'error-input': errors.title }"
        />
        <span v-if="errors.title" class="error-message">
          {{ errors.title[0] }}
        </span>
      </div>

      <div class="form-group">
        <label for="artist">Artist *</label>
        <input
          id="artist"
          v-model="formData.artist"
          type="text"
          placeholder="Enter artist name"
          :class="{ 'error-input': errors.artist }"
        />
        <span v-if="errors.artist" class="error-message">
          {{ errors.artist[0] }}
        </span>
      </div>

      <div class="form-group">
        <label for="duration">Duration (mm:ss) *</label>
        <input
          id="duration"
          v-model="durationDisplay"
          type="text"
          placeholder="00:00"
          maxlength="5"
          :class="{ 'error-input': errors.duration }"
          @input="handleDurationInput"
          @blur="validateDuration"
          @focus="$event.target.select()"
          @click="$event.target.select()"
        />
        <span v-if="errors.duration" class="error-message">
          {{ errors.duration[0] }}
        </span>
        <span v-else class="help-text">Format: mm:ss (e.g., 03:45)</span>
      </div>

      <div class="form-group">
        <label for="isrc">ISRC (optional)</label>
        <input
          id="isrc"
          v-model="formData.isrc"
          type="text"
          placeholder="US-ABC-12-34567"
          pattern="[A-Z]{2}-[A-Z0-9]{3}-[0-9]{2}-[0-9]{5}"
          :class="{ 'error-input': errors.isrc }"
        />
        <span v-if="errors.isrc" class="error-message">
          {{ errors.isrc[0] }}
        </span>
        <span v-else class="help-text">Format: XX-XXX-XX-XXXXX</span>
      </div>

      <div v-if="errors.general" class="error-message general-error">
        {{ errors.general[0] }}
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit" :disabled="submitting">
          {{ submitting ? 'Saving...' : (isEditing ? 'Update Track' : 'Create Track') }}
        </button>
        <button
          v-if="isEditing"
          type="button"
          class="btn-cancel"
          @click="handleCancel"
          :disabled="submitting"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';

const props = defineProps({
  editingTrack: {
    type: Object,
    default: null,
  },
  parseDuration: {
    type: Function,
    required: true,
  },
  formatDuration: {
    type: Function,
    required: true,
  },
});

const emit = defineEmits(['submit', 'cancel']);

const formData = ref({
  title: '',
  artist: '',
  duration: 0,
  isrc: '',
});

const durationDisplay = ref('00:00');
const errors = ref({});
const submitting = ref(false);
const isEditing = ref(false);

// Define resetForm before using it in the watcher
const resetForm = () => {
  formData.value = {
    title: '',
    artist: '',
    duration: 0,
    isrc: '',
  };
  durationDisplay.value = '00:00';
  errors.value = {};
  isEditing.value = false;
};

// Watch for editing track changes
watch(() => props.editingTrack, (track) => {
  if (track) {
    formData.value = {
      title: track.title,
      artist: track.artist,
      duration: track.duration,
      isrc: track.isrc || '',
    };
    durationDisplay.value = props.formatDuration(track.duration);
    isEditing.value = true;
    errors.value = {};
  } else {
    resetForm();
  }
}, { immediate: true });

const handleDurationInput = (event) => {
  let value = event.target.value;
  
  // Remove all non-digit characters except colon
  value = value.replace(/[^\d:]/g, '');
  
  // Remove multiple colons
  const parts = value.split(':');
  if (parts.length > 2) {
    value = parts[0] + ':' + parts.slice(1).join('');
  }
  
  // Auto-add colon after 2 digits if not present
  if (value.length === 2 && !value.includes(':')) {
    value = value + ':';
  }
  
  // Split into minutes and seconds
  const [minutes, seconds] = value.split(':');
  
  if (minutes !== undefined && seconds !== undefined) {
    // Limit minutes to 59
    let mins = parseInt(minutes) || 0;
    if (mins > 59) {
      mins = 59;
    }
    
    // Limit seconds to 59
    let secs = parseInt(seconds) || 0;
    if (secs > 59) {
      secs = 59;
    }
    
    // Pad seconds to 2 digits if complete
    const secsStr = seconds.length >= 2 ? String(secs).padStart(2, '0') : seconds;
    value = String(mins) + ':' + secsStr;
  } else if (minutes !== undefined && value.includes(':')) {
    // Just minutes with colon
    let mins = parseInt(minutes) || 0;
    if (mins > 59) {
      mins = 59;
    }
    value = String(mins) + ':';
  }
  
  durationDisplay.value = value;
};

const validateDuration = () => {
  const duration = props.parseDuration(durationDisplay.value);
  formData.value.duration = duration;
  
  // Reformat to ensure consistent display
  if (duration > 0) {
    durationDisplay.value = props.formatDuration(duration);
  }
};

const validateForm = () => {
  const newErrors = {};
  
  if (!formData.value.title?.trim()) {
    newErrors.title = ['Title is required'];
  }
  
  if (!formData.value.artist?.trim()) {
    newErrors.artist = ['Artist is required'];
  }
  
  if (!formData.value.duration || formData.value.duration <= 0) {
    newErrors.duration = ['Duration is required and must be greater than 0'];
  }
  
  if (formData.value.isrc && formData.value.isrc.trim()) {
    const isrcPattern = /^[A-Z]{2}-[A-Z0-9]{3}-\d{2}-\d{5}$/;
    if (!isrcPattern.test(formData.value.isrc.trim())) {
      newErrors.isrc = ['ISRC must match format XX-XXX-XX-XXXXX'];
    }
  }
  
  errors.value = newErrors;
  return Object.keys(newErrors).length === 0;
};

const handleSubmit = async () => {
  validateDuration();
  
  if (!validateForm()) {
    return;
  }
  
  submitting.value = true;
  
  const trackData = {
    title: formData.value.title.trim(),
    artist: formData.value.artist.trim(),
    duration: formData.value.duration,
    isrc: formData.value.isrc?.trim() || '',
  };
  
  const wasEditing = isEditing.value;
  
  // Emit the event - the parent will handle it and set editingTrack to null on success
  emit('submit', trackData);
  
  // Wait a bit for the parent to process
  await new Promise(resolve => setTimeout(resolve, 100));
  
  submitting.value = false;
  
  // If we were creating (not editing) and editingTrack is still null,
  // the form should already be reset by the watcher when parent sets editingTrack to null
  // But since editingTrack was already null, we need to reset manually
  if (!wasEditing && !props.editingTrack) {
    resetForm();
  }
};

const handleCancel = () => {
  resetForm();
  emit('cancel');
};
</script>

<style scoped>
.track-form {
  background-color: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

h2 {
  margin-top: 0;
  margin-bottom: 1.5rem;
  color: #2c3e50;
}

.form-group {
  margin-bottom: 1.25rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #2c3e50;
  font-size: 0.875rem;
}

input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e0e0e0;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.2s;
  box-sizing: border-box;
}

input:focus {
  outline: none;
  border-color: #42b983;
}

input.error-input {
  border-color: #c62828;
}

.error-message {
  display: block;
  color: #c62828;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.general-error {
  padding: 0.75rem;
  background-color: #ffebee;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.help-text {
  display: block;
  color: #757575;
  font-size: 0.75rem;
  margin-top: 0.25rem;
}

.form-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

button {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-submit {
  background-color: #42b983;
  color: white;
  flex: 1;
}

.btn-submit:hover:not(:disabled) {
  background-color: #3aa876;
}

.btn-submit:disabled {
  background-color: #9e9e9e;
  cursor: not-allowed;
}

.btn-cancel {
  background-color: #757575;
  color: white;
}

.btn-cancel:hover:not(:disabled) {
  background-color: #616161;
}

.btn-cancel:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

button:active:not(:disabled) {
  transform: translateY(1px);
}
</style>
