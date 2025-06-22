<?php
session_start();

// Ensure session cookie path is set to root
ini_set('session.cookie_path', '/');

// Debug: Check if user_id is set
if (!isset($_SESSION['user_id'])) {
    error_log("gig-creation.php: user_id not set in session. Session data: " . print_r($_SESSION, true));
    header("Location: login.php");
    exit();
}

// Include database connection
require_once('../config/database.php');

// Get all services for the dropdown
$services_query = "SELECT * FROM services ORDER BY name";
$services_result = $conn->query($services_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Gig</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Create New Gig</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger">
                                <?php
                                if ($_GET['error'] === 'missing_fields') {
                                    echo "Please fill in all required fields.";
                                } elseif ($_GET['error'] === 'image_required') {
                                    echo "Please select an image for your gig. Image upload is required.";
                                } elseif ($_GET['error'] === 'invalid_file_type') {
                                    echo "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
                                } elseif ($_GET['error'] === 'not_an_image') {
                                    echo "File is not a valid image.";
                                } elseif ($_GET['error'] === 'upload_failed') {
                                    echo "Failed to upload image.";
                                } elseif ($_GET['error'] === 'dir_creation_failed') {
                                    echo "Failed to create upload directory. Please check server permissions.";
                                } elseif ($_GET['error'] === 'dir_not_writable') {
                                    echo "Upload directory is not writable. Please check server permissions.";
                                } elseif ($_GET['error'] === 'temp_file_missing') {
                                    echo "Temporary upload file is missing. Check server configuration.";
                                } else {
                                    echo "An error occurred while creating the gig.";
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        <form action="process-gig.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="gig_title" class="form-label">Gig Title</label>
                                <input type="text" class="form-control" id="gig_title" name="gig_title" required>
                            </div>

                            <div class="mb-3">
                                <label for="service_id" class="form-label">Service Category</label>
                                <select class="form-select" id="service_id" name="service_id" required>
                                    <option value="">Select a service category</option>
                                    <?php while ($service = $services_result->fetch_assoc()): ?>
                                        <option value="<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="sub_service_id" class="form-label">Sub Service</label>
                                <select class="form-select" id="sub_service_id" name="sub_service_id" required disabled>
                                    <option value="">First select a service category</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="gig_description" class="form-label">Description</label>
                                <textarea class="form-control" id="gig_description" name="gig_description" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="gig_pricing" class="form-label">Price (PKR)</label>
                                <input type="number" class="form-control" id="gig_pricing" name="gig_pricing" step="0.01" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="gig_image" class="form-label">Gig Image *</label>
                                <input type="file" class="form-control" id="gig_image" name="gig_image" accept="image/*" required>
                                <small class="text-muted">Recommended size: 800x600 pixels. Image upload is required.</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Create Gig</button>
                                <a href="dashboard.php?page=my-services" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('service_id').addEventListener('change', function() {
        const serviceId = this.value;
        const subServiceSelect = document.getElementById('sub_service_id');
        
        if (serviceId) {
            subServiceSelect.disabled = false;
            fetch('get-sub-services.php?service_id=' + serviceId)
                .then(response => response.json())
                .then(data => {
                    subServiceSelect.innerHTML = '<option value="">Select a sub service</option>';
                    data.forEach(subService => {
                        const option = document.createElement('option');
                        option.value = subService.id;
                        option.textContent = subService.name;
                        subServiceSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching sub-services:', error);
                    subServiceSelect.innerHTML = '<option value="">Error loading sub-services</option>';
                });
        } else {
            subServiceSelect.disabled = true;
            subServiceSelect.innerHTML = '<option value="">First select a service category</option>';
        }
    });
    </script>
</body>
</html>