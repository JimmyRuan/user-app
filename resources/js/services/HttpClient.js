import axios from 'axios';
import { refreshCsrfToken } from './csrfTokenService'; // Assuming the function is in csrfTokenService.js


// Create an Axios instance
const httpClient = axios.create({
    baseURL: '/', // Set your base URL here
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
});

// Add a response interceptor to handle CSRF token expiration
httpClient.interceptors.response.use(
    (response) => {
        // Return response if no errors occur
        return response;
    },
    async (error) => {
        if (error.response && error.response.status === 419) {
            // CSRF token has expired
            try {
                console.log('Refreshing CSRF token...');
                // Retry the original request with the new CSRF token
                error.config.headers['X-CSRF-TOKEN'] = await refreshCsrfToken();
                return httpClient.request(error.config);
            } catch (refreshError) {
                console.error('Failed to refresh CSRF token:', refreshError);
                return Promise.reject(refreshError);
            }
        }
        // Reject any other errors
        return Promise.reject(error);
    }
);

export default httpClient;
