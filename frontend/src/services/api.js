import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

export const trackApi = {
  /**
   * Get all tracks
   */
  async getTracks() {
    const response = await api.get('/tracks');
    return response.data;
  },

  /**
   * Create a new track
   */
  async createTrack(trackData) {
    const response = await api.post('/tracks', trackData);
    return response.data;
  },

  /**
   * Update an existing track
   */
  async updateTrack(id, trackData) {
    const response = await api.put(`/tracks/${id}`, trackData);
    return response.data;
  },
};

export default api;
