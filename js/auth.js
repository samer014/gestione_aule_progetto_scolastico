class AuthManager {
    constructor() {
        this.baseUrl = '/API/auth/token.php';
        this.token = localStorage.getItem('access_token');
    }
    
    async login(userId, userData = {}) {
        try {
            const response = await fetch(`${this.baseUrl}/generate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    client_id: 'web_portal',
                    user_data: userData
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.token = data.data.access_token;
                localStorage.setItem('access_token', this.token);
                localStorage.setItem('token_expires', data.data.expires_at);
                return data.data;
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            console.error('Login error:', error);
            throw error;
        }
    }
    
    async validateToken() {
        if (!this.token) return false;
        
        try {
            const response = await fetch(`${this.baseUrl}/validate`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.token}`
                }
            });
            
            const data = await response.json();
            return data.success && data.data.valid;
        } catch (error) {
            console.error('Token validation error:', error);
            return false;
        }
    }
    
    async logout() {
        if (this.token) {
            try {
                await fetch(`${this.baseUrl}/revoke`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${this.token}`
                    }
                });
            } catch (error) {
                console.error('Logout error:', error);
            }
        }
        
        this.token = null;
        localStorage.removeItem('access_token');
        localStorage.removeItem('token_expires');
    }
    
    getAuthHeaders() {
        return this.token ? { 'Authorization': `Bearer ${this.token}` } : {};
    }
    
    async apiCall(url, options = {}) {
        const headers = {
            'Content-Type': 'application/json',
            ...this.getAuthHeaders(),
            ...options.headers
        };
        
        const response = await fetch(url, {
            ...options,
            headers
        });
        
        if (response.status === 401) {
            // Token scaduto, redirect al login
            this.logout();
            window.location.href = '/login.html';
            return;
        }
        
        return response;
    }
}

// Uso:
const auth = new AuthManager();

// Login
auth.login(123, { username: 'mario', role: 'admin' })
    .then(tokenData => {
        console.log('Login successful:', tokenData);
        // Redirect to dashboard
    })
    .catch(error => {
        console.error('Login failed:', error);
    });

// Chiamata API protetta
auth.apiCall('/API/prenotazioni/index.php')
    .then(response => response.json())
    .then(data => console.log(data));