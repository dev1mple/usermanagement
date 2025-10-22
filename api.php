<?php
require_once 'config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $pdo = getDBConnection();
    
    switch($action) {
        case 'login':
            if ($method === 'POST') {
                handleLogin($pdo);
            }
            break;
            
        case 'register':
            if ($method === 'POST') {
                handleRegister($pdo);
            }
            break;
            
        case 'get_users':
            if ($method === 'GET') {
                handleGetUsers($pdo);
            }
            break;
            
        case 'add_user':
            if ($method === 'POST') {
                handleAddUser($pdo);
            }
            break;
            
        case 'check_username':
            if ($method === 'GET') {
                handleCheckUsername($pdo);
            }
            break;
            
        case 'search_users':
            if ($method === 'GET') {
                handleSearchUsers($pdo);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleLogin($pdo) {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username and password are required']);
        return;
    }
    
    $stmt = $pdo->prepare("SELECT id, username, firstname, lastname, is_admin, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['is_admin'] = (bool)$user['is_admin'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname'],
                'is_admin' => (bool)$user['is_admin']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
}

function handleRegister($pdo) {
    $username = sanitizeInput($_POST['username'] ?? '');
    $firstname = sanitizeInput($_POST['firstname'] ?? '');
    $lastname = sanitizeInput($_POST['lastname'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($firstname) || empty($lastname) || empty($password) || empty($confirm_password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    
    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
        return;
    }
    
    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        return;
    }
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        return;
    }
    
    // Insert new user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, password) VALUES (?, ?, ?, ?)");
    
    if ($stmt->execute([$username, $firstname, $lastname, $hashed_password])) {
        echo json_encode(['success' => true, 'message' => 'Registration successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
}

function handleGetUsers($pdo) {
    if (!isLoggedIn() || !isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    $search = $_GET['search'] ?? '';
    $sql = "SELECT id, username, firstname, lastname, is_admin, date_added FROM users";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " WHERE username LIKE ? OR firstname LIKE ? OR lastname LIKE ?";
        $searchTerm = "%$search%";
        $params = [$searchTerm, $searchTerm, $searchTerm];
    }
    
    $sql .= " ORDER BY date_added DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'users' => $users]);
}

function handleAddUser($pdo) {
    if (!isLoggedIn() || !isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        return;
    }
    
    $username = sanitizeInput($_POST['username'] ?? '');
    $firstname = sanitizeInput($_POST['firstname'] ?? '');
    $lastname = sanitizeInput($_POST['lastname'] ?? '');
    $password = $_POST['password'] ?? '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    // Validation
    if (empty($username) || empty($firstname) || empty($lastname) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    
    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
        return;
    }
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        return;
    }
    
    // Insert new user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, password, is_admin) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$username, $firstname, $lastname, $hashed_password, $is_admin])) {
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add user']);
    }
}

function handleCheckUsername($pdo) {
    $username = sanitizeInput($_GET['username'] ?? '');
    
    if (empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Username is required']);
        return;
    }
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $exists = $stmt->fetch() !== false;
    
    echo json_encode(['success' => true, 'exists' => $exists]);
}

function handleSearchUsers($pdo) {
    $search = sanitizeInput($_GET['search'] ?? '');
    
    if (empty($search) || strlen($search) < 2) {
        echo json_encode(['success' => true, 'users' => []]);
        return;
    }
    
    $searchTerm = "%$search%";
    $stmt = $pdo->prepare("SELECT id, username, firstname, lastname, is_admin FROM users WHERE username LIKE ? OR firstname LIKE ? OR lastname LIKE ? ORDER BY username LIMIT 10");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'users' => $users]);
}
?>
