<?php
require_once 'config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch($action) {
    case 'login':
        handleLogin();
        break;
    case 'register':
        handleRegister();
        break;
    case 'getUsers':
        handleGetUsers();
        break;
    case 'addUser':
        handleAddUser();
        break;
    case 'checkUsername':
        handleCheckUsername();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function handleLogin() {
    global $pdo;
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username and password are required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, firstname, lastname, is_admin, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'is_admin' => $user['is_admin']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleRegister() {
    global $pdo;
    
    $username = $_POST['username'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($firstname) || empty($lastname) || empty($password) || empty($confirmPassword)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    
    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
        return;
    }
    
    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        return;
    }
    
    try {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            return;
        }
        
        // Insert new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $firstname, $lastname, $hashedPassword]);
        
        echo json_encode(['success' => true, 'message' => 'Registration successful']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleGetUsers() {
    global $pdo;
    
    if (!isLoggedIn() || !isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    $search = $_POST['search'] ?? '';
    
    try {
        if (!empty($search)) {
            $stmt = $pdo->prepare("SELECT id, username, firstname, lastname, is_admin, date_added FROM users WHERE username LIKE ? OR firstname LIKE ? OR lastname LIKE ? ORDER BY date_added DESC");
            $searchTerm = "%$search%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        } else {
            $stmt = $pdo->prepare("SELECT id, username, firstname, lastname, is_admin, date_added FROM users ORDER BY date_added DESC");
            $stmt->execute();
        }
        
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'users' => $users]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleAddUser() {
    global $pdo;
    
    if (!isLoggedIn() || !isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    $username = $_POST['username'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
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
    
    try {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            return;
        }
        
        // Insert new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, password, is_admin) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $firstname, $lastname, $hashedPassword, $is_admin]);
        
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleCheckUsername() {
    global $pdo;
    
    $username = $_POST['username'] ?? '';
    
    if (empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Username is required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $exists = $stmt->fetch() ? true : false;
        
        echo json_encode(['success' => true, 'exists' => $exists]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>
