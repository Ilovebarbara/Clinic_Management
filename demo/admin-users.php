<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load the system
require_once '../minimal-system.php';

// Check admin authentication
if (!auth() || auth()['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $db = db();
        
        switch ($_POST['action']) {
            case 'create_user':
                $username = $_POST['username'] ?? '';
                $email = $_POST['email'] ?? '';
                $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
                $first_name = $_POST['first_name'] ?? '';
                $last_name = $_POST['last_name'] ?? '';
                $role = $_POST['role'] ?? 'staff';
                
                try {
                    $stmt = $db->prepare("INSERT INTO users (username, email, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$username, $email, $password, $first_name, $last_name, $role]);
                    $_SESSION['success'] = "User created successfully!";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error creating user: " . $e->getMessage();
                }
                break;
                
            case 'delete_user':
                $userId = $_POST['user_id'] ?? 0;
                try {
                    $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
                    $stmt->execute([$userId]);
                    $_SESSION['success'] = "User deleted successfully!";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
                }
                break;
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Get all users
$db = db();
$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

$message = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #F9F1F0 0%, #FADCD9 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #F79489 0%, #F8AFA6 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.5);
            color: #666;
            border: 1px solid rgba(247, 148, 137, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .content-panel {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(247, 148, 137, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.6);
            font-family: inherit;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 12px;
            overflow: hidden;
        }

        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(247, 148, 137, 0.2);
        }

        .users-table th {
            background: rgba(247, 148, 137, 0.1);
            font-weight: 600;
            color: #333;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-admin {
            background: #dcfce7;
            color: #166534;
        }

        .status-doctor {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-nurse {
            background: #fef3c7;
            color: #d97706;
        }

        .status-staff {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .user-actions {
            display: flex;
            gap: 0.5rem;
        }

        .back-link {
            color: #F79489;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-link:hover {
            color: #e67e73;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/admin-dashboard" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Admin Dashboard
        </a>

        <div class="header">
            <h1 class="page-title">User Management</h1>
            <button class="btn btn-primary" onclick="toggleCreateForm()">
                <i class="fas fa-plus"></i>
                Add New User
            </button>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Create User Form -->
        <div class="content-panel" id="createForm" style="display: none;">
            <h3 style="margin-bottom: 1rem; color: #333;">Create New User</h3>
            <form method="POST">
                <input type="hidden" name="action" value="create_user">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="staff">Staff</option>
                            <option value="doctor">Doctor</option>
                            <option value="nurse">Nurse</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Create User
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="toggleCreateForm()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Users List -->
        <div class="content-panel">
            <h3 style="margin-bottom: 1rem; color: #333;">System Users (<?php echo count($users); ?>)</h3>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $user['role']; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <div class="user-actions">
                                <?php if ($user['role'] !== 'admin'): ?>
                                <form method="POST" style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <span style="color: #666; font-size: 0.8rem;">Protected</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleCreateForm() {
            const form = document.getElementById('createForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
