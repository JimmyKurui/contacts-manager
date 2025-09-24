import axios from 'axios';

const baseURL = process.env.APP_URL || '';

const api = axios.create({
  baseURL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  withCredentials: true,
});

export default api;
