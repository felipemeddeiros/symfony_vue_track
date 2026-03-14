<template>
  <div id="app">
    <header>
      <h1>🎵 Track Manager</h1>
      <p class="subtitle">Manage your music tracks with ease</p>
    </header>

    <main class="container">
      <TrackForm
        :editing-track="null"
        :parse-duration="parseDuration"
        :format-duration="formatDuration"
        @submit="handleFormSubmit"
      />

      <TrackList
        :tracks="tracks"
        :loading="loading"
        :error="error"
        :format-duration="formatDuration"
        @edit="handleEdit"
      />
    </main>

    <!-- Edit Modal -->
    <Modal
      :show="showEditModal"
      title="Edit Track"
      @close="handleCloseModal"
    >
      <TrackForm
        :editing-track="editingTrack"
        :parse-duration="parseDuration"
        :format-duration="formatDuration"
        @submit="handleFormSubmit"
        @cancel="handleCloseModal"
      />
    </Modal>

    <footer>
      <p>Built with Symfony + Vue.js</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import TrackForm from './components/TrackForm.vue';
import TrackList from './components/TrackList.vue';
import Modal from './components/Modal.vue';
import { useTracks } from './composables/useTracks';

const {
  tracks,
  loading,
  error,
  fetchTracks,
  addTrack,
  updateTrack,
  formatDuration,
  parseDuration,
} = useTracks();

const editingTrack = ref(null);
const showEditModal = ref(false);

// Fetch tracks on component mount
onMounted(() => {
  fetchTracks();
});

// Handle form submission (create or update)
const handleFormSubmit = async (trackData) => {
  let result;
  
  if (editingTrack.value) {
    // Update existing track
    result = await updateTrack(editingTrack.value.id, trackData);
  } else {
    // Create new track
    result = await addTrack(trackData);
  }
  
  if (result.success) {
    editingTrack.value = null;
    showEditModal.value = false;
  }
  
  return result;
};

// Handle edit button click
const handleEdit = (track) => {
  editingTrack.value = track;
  showEditModal.value = true;
};

// Handle modal close
const handleCloseModal = () => {
  editingTrack.value = null;
  showEditModal.value = false;
};
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
    Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  padding: 2rem 1rem;
}

#app {
  max-width: 1200px;
  margin: 0 auto;
}

header {
  text-align: center;
  color: white;
  margin-bottom: 2rem;
}

header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.subtitle {
  font-size: 1.125rem;
  opacity: 0.9;
}

.container {
  background-color: #f5f5f5;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

footer {
  text-align: center;
  color: white;
  margin-top: 2rem;
  opacity: 0.8;
  font-size: 0.875rem;
}

@media (max-width: 768px) {
  body {
    padding: 1rem 0.5rem;
  }

  header h1 {
    font-size: 2rem;
  }

  .container {
    padding: 1rem;
  }
}
</style>
