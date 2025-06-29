<?php
session_start();

// Ensure session cookie path is set to root
ini_set('session.cookie_path', '/');

// Debug: Check if user_id is set
if (!isset($_SESSION['user_id'])) {
    error_log("gig-edit.php: user_id not set in session. Session data: " . print_r($_SESSION, true));
    header("Location: login.php");
    exit();
}

// Include database connection
require_once('../config/database.php');

// Check if gig ID is provided
if (!isset($_GET['id'])) {
    header("Location: dashboard.php?page=my-services");
    exit();
}

$gig_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get the gig details
$gig_query = "SELECT * FROM gigs WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($gig_query);
$stmt->bind_param("ii", $gig_id, $user_id);
$stmt->execute();
$gig_result = $stmt->get_result();

if ($gig_result->num_rows === 0) {
    header("Location: dashboard.php?page=my-services");
    exit();
}

$gig = $gig_result->fetch_assoc();

// Get all services for the dropdown
$services_query = "SELECT * FROM services ORDER BY name";
$services_result = $conn->query($services_query);

// Get sub-services for the selected service
$sub_services_query = "SELECT * FROM sub_services WHERE service_id = ? ORDER BY name";
$stmt = $conn->prepare($sub_services_query);
$stmt->bind_param("i", $gig['service_id']);
$stmt->execute();
$sub_services_result = $stmt->get_result();

// Set the page title for the dashboard
$page_title = "Edit Gig";
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Edit Gig</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            if ($_GET['error'] === 'missing_fields') {
                                echo "Please fill in all required fields.";
                            } elseif ($_GET['error'] === 'invalid_file_type') {
                                echo "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
                            } elseif ($_GET['error'] === 'not_an_image') {
                                echo "File is not a valid image.";
                            } elseif ($_GET['error'] === 'upload_failed') {
                                echo "Failed to upload image.";
                            } elseif ($_GET['error'] === 'update_failed') {
                                echo "Failed to update the gig. Please try again.";
                            } else {
                                echo "An error occurred while updating the gig.";
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Debug output -->
                    <?php if (isset($_GET['debug'])): ?>
                        <div class="alert alert-info">
                            <pre><?php print_r($_POST); ?></pre>
                        </div>
                    <?php endif; ?>

                    <form action="dashboard.php?page=process-gig-update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="gig_id" value="<?php echo $gig_id; ?>">
                        
                        <div class="mb-3">
                            <label for="gig_title" class="form-label">Gig Title</label>
                            <input type="text" class="form-control" id="gig_title" name="gig_title" value="<?php echo htmlspecialchars($gig['gig_title']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="service_id" class="form-label">Service Category</label>
                            <select class="form-select" id="service_id" name="service_id" required>
                                <option value="">Select a service category</option>
                                <?php while ($service = $services_result->fetch_assoc()): ?>
                                    <option value="<?php echo $service['id']; ?>" <?php echo ($service['id'] == $gig['service_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($service['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="sub_service_id" class="form-label">Sub Service</label>
                            <select class="form-select" id="sub_service_id" name="sub_service_id" required>
                                <option value="">Select a sub service</option>
                                <?php while ($sub_service = $sub_services_result->fetch_assoc()): ?>
                                    <option value="<?php echo $sub_service['id']; ?>" <?php echo ($sub_service['id'] == $gig['sub_service_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sub_service['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="gig_description" class="form-label">Description</label>
                            <textarea class="form-control" id="gig_description" name="gig_description" rows="4" required><?php echo htmlspecialchars($gig['gig_description']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="gig_pricing" class="form-label">Price (PKR)</label>
                            <input type="number" class="form-control" id="gig_pricing" name="gig_pricing" step="0.01" min="0" value="<?php echo $gig['gig_pricing']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="gig_image" class="form-label">Gig Image</label>
                            <?php if (!empty($gig['image'])): ?>
                                <div class="mb-2">
                                    <img src="<?php echo htmlspecialchars($gig['image']); ?>" alt="Current Gig Image" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="gig_image" name="gig_image" accept="image/*">
                            <small class="text-muted">Leave empty to keep the current image. Recommended size: 800x600 pixels</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Gig</button>
                            <a href="dashboard.php?page=my-services" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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