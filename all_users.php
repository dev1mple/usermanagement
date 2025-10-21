<?php
require_once 'config.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Redirect if not admin
if (!isAdmin()) {
    redirect('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users - User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        .main-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
        }
        .search-box {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }
        .search-box::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .search-box:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }
        .btn-add-user {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-add-user:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }
        .btn-logout {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
            color: white;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .badge-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .badge-user {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        .loading {
            text-align: center;
            padding: 2rem;
        }
        .spinner-border {
            color: #667eea;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-users me-2"></i>User Management
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php echo htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname']); ?>
                        <span class="badge bg-primary ms-2">Admin</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="logout()">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="main-card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="mb-0">
                            <i class="fas fa-users me-2"></i>All Users
                        </h2>
                        <p class="mb-0 mt-2">Manage and view all registered users</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <button class="btn btn-add-user" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-user-plus me-2"></i>Add New User
                        </button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control search-box" id="searchInput" placeholder="Search users...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Role</th>
                                <th>Date Added</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr>
                                <td colspan="6" class="loading">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 mb-0">Loading users...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>Add New User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                    Admin User
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load users on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value;
            loadUsers(searchTerm);
        });

        function loadUsers(search = '') {
            const formData = new FormData();
            formData.append('action', 'getUsers');
            formData.append('search', search);
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUsers(data.users);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to load users'
                });
            });
        }

        function displayUsers(users) {
            const tbody = document.getElementById('usersTableBody');
            
            if (users.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No users found</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = users.map(user => `
                <tr>
                    <td>${user.id}</td>
                    <td><strong>${user.username}</strong></td>
                    <td>${user.firstname}</td>
                    <td>${user.lastname}</td>
                    <td>
                        <span class="badge ${user.is_admin == 1 ? 'badge-admin' : 'badge-user'}">
                            ${user.is_admin == 1 ? 'Admin' : 'User'}
                        </span>
                    </td>
                    <td>${new Date(user.date_added).toLocaleDateString()}</td>
                </tr>
            `).join('');
        }

        // Add user form
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'addUser');
            
            // Client-side validation
            const username = formData.get('username');
            const firstname = formData.get('firstname');
            const lastname = formData.get('lastname');
            const password = formData.get('password');
            
            if (!username || !firstname || !lastname || !password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'All fields are required'
                });
                return;
            }
            
            if (password.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Password must be at least 8 characters long'
                });
                return;
            }
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Close modal and reload users
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                        modal.hide();
                        document.getElementById('addUserForm').reset();
                        loadUsers();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.'
                });
            });
        });

        function logout() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out of your account.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, logout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('logout.php', {
                        method: 'POST'
                    })
                    .then(() => {
                        window.location.href = 'login.php';
                    })
                    .catch(() => {
                        window.location.href = 'login.php';
                    });
                }
            });
        }
    </script>
</body>
</html>
