<?php
require_once 'config.php';

$pageTitle = 'User Management - User Management System';

// Check if user is logged in and is admin
if (!isLoggedIn()) {
    redirect('login.php');
}

if (!isAdmin()) {
    redirect('index.php');
}

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">User Management</h3>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle"></i> Add New User
                </button>
            </div>
            <div class="card-body">
                <!-- Search Bar -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search users...">
                            <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-outline-primary" id="refreshBtn">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>
                
                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Role</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="add_firstname" name="firstname" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="add_lastname" name="lastname" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="add_username" name="username" required>
                        <div class="form-text">Username must be unique</div>
                    </div>
                    <div class="mb-3">
                        <label for="add_password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="add_password" name="password" required>
                        <div class="form-text">Password must be at least 8 characters long</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="add_is_admin" name="is_admin">
                            <label class="form-check-label" for="add_is_admin">
                                Administrator privileges
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentSearch = '';

// Load users on page load
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
});

// Load users function
async function loadUsers(search = '') {
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    
    try {
        const response = await apiRequest('get_users', { search }, 'GET');
        
        if (response.success) {
            displayUsers(response.users);
        } else {
            showError('Error', response.message);
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading users</td></tr>';
        }
    } catch (error) {
        showError('Error', 'Failed to load users');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading users</td></tr>';
    }
}

// Display users in table
function displayUsers(users) {
    const tbody = document.getElementById('usersTableBody');
    
    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No users found</td></tr>';
        return;
    }
    
    tbody.innerHTML = users.map(user => `
        <tr>
            <td>${user.id}</td>
            <td>${user.username}</td>
            <td>${user.firstname}</td>
            <td>${user.lastname}</td>
            <td>
                <span class="badge ${user.is_admin ? 'bg-danger' : 'bg-secondary'}">
                    ${user.is_admin ? 'Admin' : 'User'}
                </span>
            </td>
            <td>${new Date(user.date_added).toLocaleDateString()}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="viewUser(${user.id})">
                    <i class="bi bi-eye"></i> View
                </button>
            </td>
        </tr>
    `).join('');
}

// Search functionality
document.getElementById('searchBtn').addEventListener('click', function() {
    currentSearch = document.getElementById('searchInput').value;
    loadUsers(currentSearch);
});

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        currentSearch = this.value;
        loadUsers(currentSearch);
    }
});

// Refresh functionality
document.getElementById('refreshBtn').addEventListener('click', function() {
    loadUsers(currentSearch);
});

// Add user form submission
document.getElementById('addUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Client-side validation
    if (!validateForm('addUserForm')) {
        return;
    }
    
    if (!validatePassword(data.password)) {
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
    submitBtn.textContent = 'Adding...';
    submitBtn.disabled = true;
    
    try {
        const response = await apiRequest('add_user', data);
        
        if (response.success) {
            showSuccess('User Added', 'New user has been added successfully!');
            document.getElementById('addUserForm').reset();
            bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
            loadUsers(currentSearch);
        } else {
            showError('Error', response.message);
        }
    } catch (error) {
        showError('Error', 'An error occurred while adding the user');
    } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
});

// Real-time username availability check for add user form
document.getElementById('add_username').addEventListener('blur', async function() {
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

// View user function (placeholder for future implementation)
function viewUser(userId) {
    showAlert('User Details', `Viewing details for user ID: ${userId}`, 'info');
}
</script>

<?php include 'includes/footer.php'; ?>
