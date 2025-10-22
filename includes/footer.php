    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- CoreJS (jQuery alternative) -->
    <script src="https://cdn.jsdelivr.net/npm/core-js@3.32.0/minified.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Global API functions
        async function apiRequest(action, data = null, method = 'POST') {
            const url = `api.php?action=${action}`;
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                }
            };
            
            if (data && method === 'POST') {
                options.body = new URLSearchParams(data);
            } else if (data && method === 'GET') {
                const params = new URLSearchParams(data);
                const fullUrl = `${url}&${params}`;
                try {
                    const response = await fetch(fullUrl, options);
                    return await response.json();
                } catch (error) {
                    console.error('API Error:', error);
                    return { success: false, message: 'Network error occurred' };
                }
            }
            
            try {
                const response = await fetch(url, options);
                return await response.json();
            } catch (error) {
                console.error('API Error:', error);
                return { success: false, message: 'Network error occurred' };
            }
        }
        
        // Show SweetAlert notifications
        function showAlert(title, text, icon = 'info') {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                confirmButtonText: 'OK'
            });
        }
        
        // Show success alert
        function showSuccess(title, text) {
            showAlert(title, text, 'success');
        }
        
        // Show error alert
        function showError(title, text) {
            showAlert(title, text, 'error');
        }
        
        // Show warning alert
        function showWarning(title, text) {
            showAlert(title, text, 'warning');
        }
        
        // Form validation helper
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return false;
            
            const requiredFields = form.querySelectorAll('[required]');
            for (let field of requiredFields) {
                if (!field.value.trim()) {
                    showError('Validation Error', `${field.name || field.id} is required`);
                    field.focus();
                    return false;
                }
            }
            return true;
        }
        
        // Password validation
        function validatePassword(password, confirmPassword = null) {
            if (password.length < 8) {
                showError('Password Error', 'Password must be at least 8 characters long');
                return false;
            }
            
            if (confirmPassword && password !== confirmPassword) {
                showError('Password Error', 'Passwords do not match');
                return false;
            }
            
            return true;
        }
        
        // Username availability check
        async function checkUsernameAvailability(username) {
            if (!username.trim()) return true;
            
            const response = await apiRequest('check_username', { username }, 'GET');
            if (response.success && response.exists) {
                showError('Username Error', 'Username already exists');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
