class AuthService {
    constructor() {
        this.baseUrl = 'https://your-api-url.com';
        // Don't store token in localStorage for sensitive applications
        this.token = null;
        this.refreshTimer = null;
    }

    async login(clientId, clientSecret) {
        try {
            // Prevent CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            const response = await fetch(`${this.baseUrl}/api/auth/token`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                credentials: 'same-origin', // Include cookies
                body: JSON.stringify({ 
                    client_id: this.sanitizeInput(clientId), 
                    client_secret: this.sanitizeInput(clientSecret) 
                })
            });

            const data = await response.json();
            
            if (data.access_token) {
                this.token = data.access_token;
                this.setupTokenRefresh(data.expires_in);
                return true;
            }
            return false;
        } catch (error) {
            console.error('Authentication failed:', error);
            return false;
        }
    }

    login(username, password) {
        fetch('/gestione_aule_progetto_scolastico/API/auth/token.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                clientId: username,
                clientSecret: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.access_token) {
                // Salva il token o esegui altre azioni
                console.log('Login riuscito:', data.access_token);
            } else {
                // Gestisci errore
                console.error('Errore login:', data.error);
            }
        })
        .catch(error => {
            console.error('Errore di rete:', error);
        });
    }

    sanitizeInput(input) {
        // Basic input sanitization
        return input.replace(/[<>]/g, '');
    }

    setupTokenRefresh(expiresIn) {
        // Setup refresh 5 minutes before expiration
        const refreshTime = (expiresIn - 300) * 1000;
        this.refreshTimer = setTimeout(() => this.refreshToken(), refreshTime);
    }

    async refreshToken() {
        // Implement token refresh logic
    }

    logout() {
        this.token = null;
        if (this.refreshTimer) {
            clearTimeout(this.refreshTimer);
        }
        // Notify server to invalidate token
        this.invalidateToken();
    }

    async invalidateToken() {
        if (!this.token) return;
        
        try {
            await fetch(`${this.baseUrl}/api/auth/invalidate`, {
                method: 'POST',
                headers: this.getAuthorizationHeader()
            });
        } catch (error) {
            console.error('Token invalidation failed:', error);
        }
    }

    getAuthorizationHeader() {
        return {
            'Authorization': `Bearer ${this.token}`,
            'X-Requested-With': 'XMLHttpRequest' // Protect against CSRF
        };
    }

    async makeAuthenticatedRequest(url, options = {}) {
        if (!this.token) {
            throw new Error('No token available');
        }

        const headers = {
            ...options.headers,
            ...this.getAuthorizationHeader()
        };

        try {
            const response = await fetch(url, { 
                ...options, 
                headers,
                credentials: 'same-origin' // Include cookies
            });
            
            if (response.status === 401) {
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
        this.token = null;
        if (this.refreshTimer) {
            clearTimeout(this.refreshTimer);
        }
        // Redirect to login
        window.location.href = '/login';
    }
}