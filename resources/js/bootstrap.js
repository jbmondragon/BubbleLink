import axios from 'axios';
window.axios = axios;

// Mark script-driven requests so Laravel can treat them as AJAX-style requests.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
