<?php
// Include database connection
require_once '../config/database.php';

// Get user information from database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $gender = $_POST['gender'];
    $update_password = false;
    $password = '';
    
    // Handle password update if provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_password = true;
    }

    // Handle file uploads for id_card_front and id_card_back
    $id_card_front = $user['id_card_front'];
    $id_card_back = $user['id_card_back'];
    $upload_dir = '../uploads/id_cards/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    if (isset($_FILES['id_card_front']) && $_FILES['id_card_front']['error'] === UPLOAD_ERR_OK) {
        $front_ext = pathinfo($_FILES['id_card_front']['name'], PATHINFO_EXTENSION);
        $front_name = uniqid('front_') . '.' . $front_ext;
        $front_path = $upload_dir . $front_name;
        if (move_uploaded_file($_FILES['id_card_front']['tmp_name'], $front_path)) {
            $id_card_front = 'uploads/id_cards/' . $front_name;
        }
    }
    if (isset($_FILES['id_card_back']) && $_FILES['id_card_back']['error'] === UPLOAD_ERR_OK) {
        $back_ext = pathinfo($_FILES['id_card_back']['name'], PATHINFO_EXTENSION);
        $back_name = uniqid('back_') . '.' . $back_ext;
        $back_path = $upload_dir . $back_name;
        if (move_uploaded_file($_FILES['id_card_back']['tmp_name'], $back_path)) {
            $id_card_back = 'uploads/id_cards/' . $back_name;
        }
    }

    // Update user information
    if ($update_password) {
        $update_query = "UPDATE users SET username = ?, email = ?, password = ?, gender = ?, id_card_front = ?, id_card_back = ?, bio = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sssssssi", $username, $email, $password, $gender, $id_card_front, $id_card_back, $bio, $user_id);
    } else {
        $update_query = "UPDATE users SET username = ?, email = ?, gender = ?, id_card_front = ?, id_card_back = ?, bio = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssssi", $username, $email, $gender, $id_card_front, $id_card_back, $bio, $user_id);
    }

    if ($update_stmt->execute()) {
        $success_message = "Profile updated successfully!";
        // Refresh user data
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $error_message = "Error updating profile. Please try again.";
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Profile Information</h2>
                    
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password (leave blank to keep current)</label>
                                <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="male" <?php echo ($user['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo ($user['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="id_card_front" class="form-label">ID Card Front</label>
                                <?php if (!empty($user['id_card_front'])): ?>
                                    <div class="mb-2">
                                        <img src="../<?php echo htmlspecialchars($user['id_card_front']); ?>" alt="ID Card Front" class="img-thumbnail" style="max-height: 120px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="id_card_front" name="id_card_front" accept="image/*">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="id_card_back" class="form-label">ID Card Back</label>
                                <?php if (!empty($user['id_card_back'])): ?>
                                    <div class="mb-2">
                                        <img src="../<?php echo htmlspecialchars($user['id_card_back']); ?>" alt="ID Card Back" class="img-thumbnail" style="max-height: 120px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="id_card_back" name="id_card_back" accept="image/*">
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control {
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    padding: 10px 15px;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
}

.btn-primary {
    background-color: var(--primary-color);
    border: none;
    padding: 10px 25px;
    border-radius: 8px;
    font-weight: 500;
}

.btn-primary:hover {
    background-color: #ff5252;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.alert {
    border-radius: 8px;
    margin-bottom: 20px;
}
</style> 