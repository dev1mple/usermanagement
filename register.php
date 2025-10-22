<?php
require_once 'config.php';

$pageTitle = 'Register - User Management System';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0">Create Account</h4>
            </div>
            <div class="card-body">
                <form id="registerForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="form-text">Username must be unique</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Password must be at least 8 characters long</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Create Account</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <p class="mb-0">Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Client-side validation
    if (!validateForm('registerForm')) {
        return;
    }
    
    if (!validatePassword(data.password, data.confirm_password)) {
        return;
    }
    
    // Check username availability
    const usernameAvailable = await checkUsernameAvailability(data.username);
    if (!usernameAvailable) {
        return;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Creating Account...';
    submitBtn.disabled = true;
    
    try {
        const response = await apiRequest('register', data);
        
        if (response.success) {
            showSuccess('Registration Successful', 'Your account has been created successfully!');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            showError('Registration Failed', response.message);
        }
    } catch (error) {
        showError('Error', 'An error occurred during registration');
    } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
});

// Real-time username availability check
document.getElementById('username').addEventListener('blur', async function() {
    const username = this.value.trim();
    if (username.length > 0) {
        const response = await apiRequest('check_username', { username }, 'GET');
        if (response.success && response.exists) {
            this.classList.add('is-invalid');
            showWarning('Username Taken', 'This username is already taken. Please choose another one.');
        } else {
            this.classList.remove('is-invalid');
        }
    }
});

// Real-time password confirmation check
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword.length > 0) {
        if (password === confirmPassword) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        }
    } else {
        this.classList.remove('is-valid', 'is-invalid');
    }
});
</script>

<?php include 'includes/footer.php'; ?>
