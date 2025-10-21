<?php
require_once 'config.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - User Management</title>
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
        .welcome-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            overflow: hidden;
        }
        .welcome-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }
        .welcome-body {
            padding: 3rem 2rem;
        }
        .feature-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s ease;
            border: none;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
        .btn-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-users me-2"></i>User Management
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php echo htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="logout()">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="welcome-card">
                    <div class="welcome-header">
                        <h1 class="display-4 fw-bold mb-3">
                            <i class="fas fa-hand-wave me-3"></i>
                            Hello there, <?php echo htmlspecialchars($_SESSION['firstname']); ?>!
                        </h1>
                        <p class="lead mb-0">Welcome to your user management dashboard</p>
                    </div>
                    <div class="welcome-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <div class="feature-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h4>Profile Management</h4>
                                    <p class="text-muted">Manage your personal information and account settings.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <div class="feature-icon">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <h4>Account Settings</h4>
                                    <p class="text-muted">Update your password and security preferences.</p>
                                </div>
                            </div>
                            <?php if (isAdmin()): ?>
                            <div class="col-12">
                                <div class="text-center">
                                    <h4 class="mb-3">Admin Panel</h4>
                                    <a href="all_users.php" class="btn btn-admin btn-lg">
                                        <i class="fas fa-users-cog me-2"></i>Manage All Users
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
                    // Create a simple logout by destroying session
                    fetch('logout.php', {
                        method: 'POST'
                    })
                    .then(() => {
                        window.location.href = 'login.php';
                    })
                    .catch(() => {
                        // Fallback if logout.php doesn't exist
                        window.location.href = 'login.php';
                    });
                }
            });
        }
    </script>
</body>
</html>
