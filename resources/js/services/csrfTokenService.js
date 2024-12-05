import axios from 'axios';

export async function refreshCsrfToken() {
    try {
        const response = await axios.get('/csrf-token', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });
        const newCsrfToken = response.data.csrf_token;

        // Update Axios headers
        axios.defaults.headers.common['X-CSRF-TOKEN'] = newCsrfToken;

        console.log('CSRF token refreshed successfully');
        return newCsrfToken;
    } catch (error) {
        console.error('Failed to refresh CSRF token:', error);
        throw error;
    }
}
