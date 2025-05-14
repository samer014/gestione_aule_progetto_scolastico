class AuthService {
    constructor() {
        this.baseUrl = 'https://your-api-url.com';
        this.token = localStorage.getItem('access_token');
    }

    async login(clientId, clientSecret) {
        try {
            const response = await fetch(`${this.baseUrl}/api/auth/token`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ client_id: clientId, client_secret: clientSecret })
            });

            const data = await response.json();
            
            if (data.access_token) {
                this.token = data.access_token;
                this.storeToken(data.access_token);
                return true;
            }
            return false;
        } catch (error) {
            console.error('Authentication failed:', error);
            return false;
        }
    }

    storeToken(token) {
        // Store in memory for XSS protection
        this.token = token;
        // Optional: Store in localStorage (less secure)
        localStorage.setItem('access_token', token);
    }

    getAuthorizationHeader() {
        return {
            'Authorization': `Bearer ${this.token}`
        };
    }

    async makeAuthenticatedRequest(url, options = {}) {
        const headers = {
            ...options.headers,
            ...this.getAuthorizationHeader()
        };

        try {
            const response = await fetch(url, { ...options, headers });
            
            if (response.status === 401) {
                // Token expired or invalid
                this.handleTokenExpiration();
                return null;
            }

            return response;
        } catch (error) {
            console.error('Request failed:', error);
            throw error;
        }
    }

    handleTokenExpiration() {
        localStorage.removeItem('access_token');
        this.token = null;
        // Redirect to login or refresh token
        window.location.href = '/login';
    }
}