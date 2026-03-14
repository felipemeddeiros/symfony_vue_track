import { ref } from 'vue';
import { trackApi } from '../services/api';

export function useTracks() {
  const tracks = ref([]);
  const loading = ref(false);
  const error = ref(null);

  /**
   * Fetch all tracks from the API
   */
  const fetchTracks = async () => {
    loading.value = true;
    error.value = null;
    
    try {
      const data = await trackApi.getTracks();
      tracks.value = data;
    } catch (err) {
      error.value = 'Failed to load tracks';
      console.error('Error fetching tracks:', err);
    } finally {
      loading.value = false;
    }
  };

  /**
   * Create a new track
   */
  const addTrack = async (trackData) => {
    loading.value = true;
    error.value = null;
    
    try {
      await trackApi.createTrack(trackData);
      await fetchTracks(); // Refresh the list
      return { success: true };
    } catch (err) {
      console.error('Error creating track:', err);
      
      // Handle validation errors (422)
      if (err.response && err.response.status === 422) {
        return { success: false, errors: err.response.data };
      }
      
      error.value = 'Failed to create track';
      return { success: false, errors: { general: ['Failed to create track'] } };
    } finally {
      loading.value = false;
    }
  };

  /**
   * Update an existing track
   */
  const updateTrack = async (id, trackData) => {
    loading.value = true;
    error.value = null;
    
    try {
      await trackApi.updateTrack(id, trackData);
      await fetchTracks(); // Refresh the list
      return { success: true };
    } catch (err) {
      console.error('Error updating track:', err);
      
      // Handle validation errors (422)
      if (err.response && err.response.status === 422) {
        return { success: false, errors: err.response.data };
      }
      
      // Handle not found (404)
      if (err.response && err.response.status === 404) {
        return { success: false, errors: { general: ['Track not found'] } };
      }
      
      error.value = 'Failed to update track';
      return { success: false, errors: { general: ['Failed to update track'] } };
    } finally {
      loading.value = false;
    }
  };

  /**
   * Format duration from seconds to mm:ss
   */
  const formatDuration = (seconds) => {
    if (!seconds && seconds !== 0) return '00:00';
    
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    
    return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
  };

  /**
   * Parse duration from mm:ss to seconds
   */
  const parseDuration = (mmss) => {
    if (!mmss) return 0;
    
    const parts = mmss.split(':');
    if (parts.length !== 2) return 0;
    
    const mins = parseInt(parts[0], 10) || 0;
    const secs = parseInt(parts[1], 10) || 0;
    
    return mins * 60 + secs;
  };

  return {
    tracks,
    loading,
    error,
    fetchTracks,
    addTrack,
    updateTrack,
    formatDuration,
    parseDuration,
  };
}
