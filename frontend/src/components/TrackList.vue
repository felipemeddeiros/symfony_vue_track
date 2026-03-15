<template>
  <div class="track-list">
    <h2>Track List</h2>
    
    <div v-if="loading" class="loading">Loading tracks...</div>
    
    <div v-else-if="error" class="error">{{ error }}</div>
    
    <div v-else-if="tracks.length === 0" class="empty">
      No tracks yet. Create your first track above!
    </div>
    
    <table v-else class="tracks-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Artist</th>
          <th>Duration</th>
          <th>ISRC</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="track in tracks" :key="track.id">
          <td>{{ track.title }}</td>
          <td>{{ track.artist }}</td>
          <td>{{ formatDuration(track.duration) }}</td>
          <td>{{ track.isrc || '-' }}</td>
          <td>
            <button @click="$emit('edit', track)" class="btn-edit">
              Edit
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>

const props = defineProps({
  tracks: {
    type: Array,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: null,
  },
  formatDuration: {
    type: Function,
    required: true,
  },
});

const emit = defineEmits(['edit']);
</script>

<style scoped>
.track-list {
  margin-top: 2rem;
}

h2 {
  margin-bottom: 1rem;
  color: #2c3e50;
}

.loading,
.error,
.empty {
  padding: 1rem;
  text-align: center;
  border-radius: 4px;
}

.loading {
  background-color: #e3f2fd;
  color: #1976d2;
}

.error {
  background-color: #ffebee;
  color: #c62828;
}

.empty {
  background-color: #f5f5f5;
  color: #757575;
}

.tracks-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  overflow: hidden;
}

.tracks-table thead {
  background-color: #42b983;
  color: white;
}

.tracks-table th,
.tracks-table td {
  padding: 0.75rem 1rem;
  text-align: left;
}

.tracks-table th {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.875rem;
  letter-spacing: 0.5px;
}

.tracks-table tbody tr {
  border-bottom: 1px solid #e0e0e0;
}

.tracks-table tbody tr:last-child {
  border-bottom: none;
}

.tracks-table tbody tr:hover {
  background-color: #f5f5f5;
}

.btn-edit {
  background-color: #1976d2;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.875rem;
  transition: background-color 0.2s;
}

.btn-edit:hover {
  background-color: #1565c0;
}

.btn-edit:active {
  transform: translateY(1px);
}
</style>
