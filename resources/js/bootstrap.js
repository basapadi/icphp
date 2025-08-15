import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = import.meta.env.APP_URL; // opsional
window.axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
window.axios.defaults.headers.common['Accept'] = 'application/json';
