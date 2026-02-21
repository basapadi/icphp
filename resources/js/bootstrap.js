import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = import.meta.env.APP_URL; // opsional
window.axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
window.axios.defaults.headers.common['Accept'] = 'application/json';

window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.status === 401) {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            localStorage.removeItem('ai_chat')
            localStorage.removeItem('report_schema_cache')

            window.location.href = '/login'
        }

        return Promise.reject(error)
    }
)

