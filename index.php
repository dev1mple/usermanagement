<?php
require_once 'config.php';

$pageTitle = 'Dashboard - User Management System';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

include 'includes/header.php';
?>

<style>
.user-result:hover {
    background-color: #f8f9fa;
}

#searchResults {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.user-result:last-child {
    border-bottom: none !important;
}

.user-result {
    transition: background-color 0.15s ease-in-out;
}

/* Welcome Section Styling */
.welcome-section {
    background: linear-gradient(135deg, #f10505 0%, #fa3a00 100%);
    color: white;
    padding: 2rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}

.welcome-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.welcome-title {
    color: white;
    font-weight: 700;
    font-size: 2rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 1;
}

.welcome-subtitle {
    color: rgba(255, 255, 255, 0.95);
    font-size: 1.2rem;
    font-weight: 400;
    position: relative;
    z-index: 1;
}

.welcome-badges .badge {
    font-size: 0.9rem;
    padding: 0.6rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    z-index: 1;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.welcome-time {
    text-align: right;
    position: relative;
    z-index: 1;
}

.time-display {
    margin-bottom: 0.5rem;
}

.time-value {
    font-size: 1.8rem;
    font-weight: bold;
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    font-family: 'Courier New', monospace;
}

.date-value {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
}

/* Welcome Animation */
.welcome-section {
    animation: welcomeSlideIn 0.8s ease-out;
}

@keyframes welcomeSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.welcome-title {
    animation: titleFadeIn 1s ease-out 0.3s both;
}

@keyframes titleFadeIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.welcome-badges .badge {
    animation: badgeBounce 0.6s ease-out 0.8s both;
}

@keyframes badgeBounce {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* Account Information Card */
.card.bg-light {
    border-left: 4px solid #f10505;
}

.card-title {
    color: #f10505;
    font-weight: 600;
}

/* Welcome Popup Styling */
.swal2-popup-welcome {
    border-radius: 20px !important;
    box-shadow: 0 20px 40px rgba(241, 5, 5, 0.3) !important;
}

/* Additional Welcome Effects */
.welcome-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(241, 5, 5, 0.3);
    transition: all 0.3s ease;
}

.welcome-badges .badge:hover {
    transform: scale(1.05);
    transition: all 0.2s ease;
}

/* Floating Icons */
.floating-icon {
    position: absolute;
    animation: float 3s ease-in-out infinite;
    opacity: 0.1;
    font-size: 2rem;
}

.floating-icon:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
.floating-icon:nth-child(2) { top: 20%; right: 15%; animation-delay: 1s; }
.floating-icon:nth-child(3) { bottom: 20%; left: 20%; animation-delay: 2s; }
.floating-icon:nth-child(4) { bottom: 10%; right: 10%; animation-delay: 0.5s; }

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .welcome-section {
        padding: 1.5rem;
    }
    
    .welcome-title {
        font-size: 1.5rem;
    }
    
    .welcome-time {
        text-align: left;
        margin-top: 1rem;
    }
}
</style>

<div class="row">
    <div class="col-12">
        <!-- User Search Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Search Users</h5>
            </div>
            <div class="card-body">
                <div class="position-relative">
                    <input type="text" class="form-control" id="userSearch" placeholder="Search users by username, first name, or last name...">
                    <div id="searchResults" class="position-absolute w-100 bg-white border rounded shadow-lg" style="top: 100%; z-index: 1000; display: none; max-height: 300px; overflow-y: auto;">
                        <!-- Search results will be populated here -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Section -->
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Dashboard</h3>
            </div>
            <div class="card-body">
                <!-- Welcome Section -->
                <div class="welcome-section mb-4">
                    <!-- Floating Icons -->
                    <i class="bi bi-star-fill floating-icon text-warning"></i>
                    <i class="bi bi-heart-fill floating-icon text-danger"></i>
                    <i class="bi bi-lightning-fill floating-icon text-warning"></i>
                    <i class="bi bi-trophy-fill floating-icon text-warning"></i>
                    
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="welcome-content">
                                <h2 class="welcome-title mb-3">
                                    <i class="bi bi-emoji-smile me-2"></i>
                                    Welcome back, <?php echo htmlspecialchars($_SESSION['firstname']); ?>! 
                                    <i class="bi bi-heart-fill text-warning ms-2"></i>
                                </h2>
                                <p class="welcome-subtitle mb-3">
                                    🎉 Great to see you again! You're successfully logged in to the User Management System
                                </p>
                                <div class="welcome-badges">
                                    <span class="badge bg-warning text-dark me-2">
                                        <i class="bi bi-person-fill me-1"></i>
                                        @<?php echo htmlspecialchars($_SESSION['username']); ?>
                                    </span>
                                    <span class="badge <?php echo isAdmin() ? 'bg-light text-dark' : 'bg-success'; ?>">
                                        <i class="bi bi-<?php echo isAdmin() ? 'shield-check' : 'person-check'; ?> me-1"></i>
                                        <?php echo isAdmin() ? 'Administrator' : 'User'; ?>
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <small class="text-white-50">
                                        <i class="bi bi-info-circle me-1"></i>
                                        <?php 
                                        $greeting = '';
                                        $hour = date('H');
                                        if ($hour < 12) $greeting = 'Good Morning';
                                        elseif ($hour < 17) $greeting = 'Good Afternoon';
                                        else $greeting = 'Good Evening';
                                        echo $greeting . '! Ready to manage your account?';
                                        ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="welcome-time">
                                <div class="time-display">
                                    <i class="bi bi-clock-fill me-1"></i>
                                    <small>Session Started</small>
                                </div>
                                <div class="time-value">
                                    <?php echo date('H:i'); ?>
                                </div>
                                <div class="date-value">
                                    <?php echo date('M d, Y'); ?>
                                </div>
                                <div class="mt-2">
                                    <small class="text-white-50">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        <?php echo date('l'); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Account Details Card -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle text-info me-2"></i>
                            Account Information
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Full Name:</strong> 
                                    <?php echo htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname']); ?>
                                </p>
                                <p class="mb-2">
                                    <strong>Username:</strong> 
                                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Role:</strong> 
                                    <span class="badge <?php echo isAdmin() ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo isAdmin() ? 'Administrator' : 'Regular User'; ?>
                                    </span>
                                </p>
                                <p class="mb-0">
                                    <strong>Session Status:</strong> 
                                    <span class="text-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>Active
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Quick Actions</h5>
                                <p class="card-text">Manage your account and system settings.</p>
                                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                            </div>
                        </div>
                    </div>
                    <?php if (isAdmin()): ?>
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Admin Panel</h5>
                                <p class="card-text">Access administrative functions and user management.</p>
                                <a href="all_users.php" class="btn btn-light">Manage Users</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4">
                    <h5>System Information</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Current User</h6>
                                    <p class="card-text"><?php echo htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname']); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="card-title">User Role</h6>
                                    <p class="card-text"><?php echo isAdmin() ? 'Administrator' : 'Regular User'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Session Status</h6>
                                    <p class="card-text text-success">Active</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let searchTimeout;

// Show welcome notification on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if this is a fresh login (you can enhance this with session storage)
    const isFirstVisit = !sessionStorage.getItem('welcomeShown');
    
    if (isFirstVisit) {
        setTimeout(() => {
            showWelcomeNotification();
            sessionStorage.setItem('welcomeShown', 'true');
        }, 1000);
    }
});

// Welcome notification function
function showWelcomeNotification() {
    const firstName = '<?php echo htmlspecialchars($_SESSION['firstname']); ?>';
    const role = '<?php echo isAdmin() ? 'Administrator' : 'User'; ?>';
    const roleIcon = '<?php echo isAdmin() ? 'shield-check' : 'person-check'; ?>';
    const currentHour = new Date().getHours();
    let greeting = '';
    let emoji = '';
    
    if (currentHour < 12) {
        greeting = 'Good Morning';
        emoji = '🌅';
    } else if (currentHour < 17) {
        greeting = 'Good Afternoon';
        emoji = '☀️';
    } else {
        greeting = 'Good Evening';
        emoji = '🌙';
    }
    
    Swal.fire({
        title: `${emoji} ${greeting}, ${firstName}!`,
        html: `
            <div class="text-center">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
                <h4 style="color: #f10505; margin-bottom: 1rem;">Welcome Back!</h4>
                <div style="background: linear-gradient(135deg, #f10505, #fa3a00); color: white; padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                    <i class="bi bi-${roleIcon}" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                    <p style="margin: 0; font-weight: bold;">Logged in as ${role}</p>
                </div>
                <p style="color: #666; margin-top: 1rem;">Ready to explore and manage your account? Let's get started! 🚀</p>
            </div>
        `,
        icon: 'success',
        showConfirmButton: true,
        confirmButtonText: '🚀 Let\'s Go!',
        confirmButtonColor: '#f10505',
        timer: 6000,
        timerProgressBar: true,
        allowOutsideClick: false,
        customClass: {
            popup: 'swal2-popup-welcome'
        }
    });
}

// User search functionality
document.getElementById('userSearch').addEventListener('input', function() {
    const searchTerm = this.value.trim();
    const resultsDiv = document.getElementById('searchResults');
    
    // Clear previous timeout
    clearTimeout(searchTimeout);
    
    if (searchTerm.length < 2) {
        resultsDiv.style.display = 'none';
        return;
    }
    
    // Debounce search requests
    searchTimeout = setTimeout(async () => {
        try {
            const response = await apiRequest('search_users', { search: searchTerm }, 'GET');
            
            if (response.success) {
                displaySearchResults(response.users);
            } else {
                resultsDiv.style.display = 'none';
            }
        } catch (error) {
            console.error('Search error:', error);
            resultsDiv.style.display = 'none';
        }
    }, 300);
});

// Display search results
function displaySearchResults(users) {
    const resultsDiv = document.getElementById('searchResults');
    
    if (users.length === 0) {
        resultsDiv.innerHTML = '<div class="p-3 text-muted text-center">No users found</div>';
        resultsDiv.style.display = 'block';
        return;
    }
    
    resultsDiv.innerHTML = users.map(user => `
        <div class="p-3 border-bottom user-result" style="cursor: pointer;" onclick="viewUserDetails('${user.username}', '${user.firstname}', '${user.lastname}', ${user.is_admin})">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>${user.username}</strong>
                    <br>
                    <small class="text-muted">${user.firstname} ${user.lastname}</small>
                </div>
                <span class="badge ${user.is_admin ? 'bg-danger' : 'bg-secondary'}">
                    ${user.is_admin ? 'Admin' : 'User'}
                </span>
            </div>
        </div>
    `).join('');
    
    resultsDiv.style.display = 'block';
}

// View user details from search results
function viewUserDetails(username, firstname, lastname, isAdmin) {
    const role = isAdmin ? 'Administrator' : 'Regular User';
    showAlert('User Details', `
        <strong>Username:</strong> ${username}<br>
        <strong>Name:</strong> ${firstname} ${lastname}<br>
        <strong>Role:</strong> ${role}
    `, 'info');
    
    // Clear search and hide results
    document.getElementById('userSearch').value = '';
    document.getElementById('searchResults').style.display = 'none';
}

// Hide search results when clicking outside
document.addEventListener('click', function(e) {
    const searchContainer = document.querySelector('.position-relative');
    const resultsDiv = document.getElementById('searchResults');
    
    if (!searchContainer.contains(e.target)) {
        resultsDiv.style.display = 'none';
    }
});
</script>

<?php include 'includes/footer.php'; ?>
