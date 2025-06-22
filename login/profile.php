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

// Debug profile picture path
error_log("Profile Picture Path from DB: " . ($user['profile_picture'] ?? 'null'));
if (!empty($user['profile_picture'])) {
    $absolute_path = __DIR__ . '/../' . $user['profile_picture'];
    error_log("Absolute Path: " . $absolute_path);
    error_log("File exists check: " . (file_exists($absolute_path) ? 'true' : 'false'));
}

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

    // Handle profile picture upload
    $profile_picture = $user['profile_picture'];
    $upload_dir = __DIR__ . '/../uploads/profile_pictures/';
    
    // Create upload directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $error_message = "Failed to create upload directory";
        }
    }

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_picture']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('profile_') . '.' . $ext;
            $filepath = $upload_dir . $filename;
            
            // Debug information
            error_log("Upload attempt - File: " . $_FILES['profile_picture']['name']);
            error_log("Temporary path: " . $_FILES['profile_picture']['tmp_name']);
            error_log("Target path: " . $filepath);
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filepath)) {
                $profile_picture = 'uploads/profile_pictures/' . $filename;
                error_log("File uploaded successfully. DB path: " . $profile_picture);
                error_log("Full file path: " . $filepath);
            } else {
                $error_message = "Failed to move uploaded file. Error: " . error_get_last()['message'];
                error_log("Upload failed: " . $error_message);
            }
        } else {
            $error_message = "Invalid file type. Allowed types: JPG, PNG, GIF";
            error_log("Invalid file type: " . $file_type);
        }
    } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle other upload errors
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        $error_code = $_FILES['profile_picture']['error'];
        $error_message = isset($upload_errors[$error_code]) ? $upload_errors[$error_code] : 'Unknown upload error';
        error_log("Upload error: " . $error_message);
    }

    // Handle file uploads for id_card_front and id_card_back
    $id_card_front = $user['id_card_front'];
    $id_card_back = $user['id_card_back'];
    $upload_dir = __DIR__ . '/../uploads/id_cards/';
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

    // Only proceed with update if there are no errors
    if (!isset($error_message)) {
        // Update user information
        if ($update_password) {
            $update_query = "UPDATE users SET username = ?, email = ?, password = ?, gender = ?, profile_picture = ?, id_card_front = ?, id_card_back = ?, bio = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssssssssi", $username, $email, $password, $gender, $profile_picture, $id_card_front, $id_card_back, $bio, $user_id);
        } else {
            $update_query = "UPDATE users SET username = ?, email = ?, gender = ?, profile_picture = ?, id_card_front = ?, id_card_back = ?, bio = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("sssssssi", $username, $email, $gender, $profile_picture, $id_card_front, $id_card_back, $bio, $user_id);
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
            error_log("Database update error: " . $conn->error);
        }
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
                            <div class="col-12 mb-4 text-center">
                                <div class="profile-picture-container">
                                    <?php
                                    $profile_picture = $user['profile_picture'] ?? '';
                                    $profile_picture_path = '';
                                    
                                    if (!empty($profile_picture)) {
                                        // Check if the path starts with 'uploads/'
                                        if (strpos($profile_picture, 'uploads/') === 0) {
                                            $profile_picture_path = '../' . $profile_picture;
                                        } else {
                                            $profile_picture_path = $profile_picture;
                                        }
                                        
                                        // Debug the path
                                        error_log("Display Path: " . $profile_picture_path);
                                        
                                        // Check if file exists
                                        $absolute_path = __DIR__ . '/../' . $profile_picture;
                                        if (file_exists($absolute_path)) {
                                            echo '<img src="' . htmlspecialchars($profile_picture_path) . '" alt="Profile Picture" class="profile-picture">';
                                        } else {
                                            error_log("Profile picture file not found at: " . $absolute_path);
                                            echo '<div class="profile-picture-placeholder"><i class="fas fa-user"></i></div>';
                                        }
                                    } else {
                                        echo '<div class="profile-picture-placeholder"><i class="fas fa-user"></i></div>';
                                    }
                                    ?>
                                    <div class="mt-3">
                                        <label for="profile_picture" class="btn btn-outline-primary">
                                            <i class="fas fa-camera me-2"></i>Change Profile Picture
                                        </label>
                                        <input type="file" class="form-control d-none" id="profile_picture" name="profile_picture" accept="image/*">
                                    </div>
                                </div>
                            </div>

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
                                        <img src="../registration/<?php echo htmlspecialchars($user['id_card_front']); ?>" alt="ID Card Front" class="img-thumbnail" style="max-height: 120px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="id_card_front" name="id_card_front" accept="image/*">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="id_card_back" class="form-label">ID Card Back</label>
                                <?php if (!empty($user['id_card_back'])): ?>
                                    <div class="mb-2">
                                        <img src="../registration/<?php echo htmlspecialchars($user['id_card_back']); ?>" alt="ID Card Back" class="img-thumbnail" style="max-height: 120px;">
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

.profile-picture-container {
    margin-bottom: 20px;
}

.profile-picture {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary-color);
}

.profile-picture-placeholder {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    border: 3px solid var(--primary-color);
}

.profile-picture-placeholder i {
    font-size: 64px;
    color: #999;
}

.btn-outline-primary {
    color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-outline-primary:hover {
    background-color: var(--primary-color);
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileInput = document.getElementById('profile_picture');
    const previewContainer = document.querySelector('.profile-picture-container');
    
    profileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file type
            if (!file.type.match('image.*')) {
                alert('Please select an image file');
                return;
            }
            
            // Check file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size should be less than 2MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                // Remove existing preview
                const existingImg = previewContainer.querySelector('img');
                const existingPlaceholder = previewContainer.querySelector('.profile-picture-placeholder');
                
                if (existingImg) {
                    existingImg.remove();
                }
                if (existingPlaceholder) {
                    existingPlaceholder.remove();
                }

                // Create new preview image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Profile Picture';
                img.className = 'profile-picture';
                
                // Insert the new image before the upload button div
                const uploadButton = previewContainer.querySelector('.mt-3');
                previewContainer.insertBefore(img, uploadButton);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script> 