<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in and is a freelancer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'freelancer') {
    header("Location: ../Login/index.php");  // Redirect if not a freelancer
    exit(); 
}

// Database connection
$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "freelance_website"; // Your database name
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch services and sub-services
$services_query = "SELECT id, name FROM services WHERE status = 'active'";
$services_result = $conn->query($services_query);
$services = [];
while ($row = $services_result->fetch_assoc()) {
    $services[] = $row;
}

$sub_services_query = "SELECT id, service_id, name FROM sub_services WHERE status = 'active'";
$sub_services_result = $conn->query($sub_services_query);
$sub_services = [];
while ($row = $sub_services_result->fetch_assoc()) {
    $sub_services[] = $row;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $title = htmlspecialchars(trim($_POST['gig-title']));
    $gig_description = htmlspecialchars(trim($_POST['gig-description']));
    $service_id = (int)$_POST['service_id'];
    $sub_service_id = (int)$_POST['sub_service_id'];
    $pricing = htmlspecialchars(trim($_POST['pricing']));
    $tags = isset($_POST['tags']) ? htmlspecialchars(trim($_POST['tags'])) : null;

    // Handle image upload
    $file_path = null;
    if (isset($_FILES['gig-image']) && $_FILES['gig-image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 5 * 1024 * 1024;  // 5 MB

        if (!in_array($_FILES['gig-image']['type'], $allowed_types)) {
            echo "Invalid image format. Please upload JPEG or PNG.";
            exit();
        }
        if ($_FILES['gig-image']['size'] > $max_size) {
            echo "Image size exceeds 5MB. Please upload a smaller image.";
            exit();
        }

        // Create uploads directory if it doesn't exist
        $upload_dir = __DIR__ . '/uploads';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique filename
        $file_extension = pathinfo($_FILES['gig-image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '_' . time() . '.' . $file_extension;
        $file_path = 'uploads/' . $file_name;
        $absolute_path = $upload_dir . '/' . $file_name;

        // Move the uploaded file
        if (!move_uploaded_file($_FILES['gig-image']['tmp_name'], $absolute_path)) {
            $error = error_get_last();
            echo "Error uploading file: " . ($error ? $error['message'] : 'Unknown error');
            exit();
        }
    }

    // Insert gig data into the database
    $stmt = $conn->prepare("INSERT INTO gigs (user_id, service_id, sub_service_id, gig_title, gig_description, gig_pricing, tags, gig_images)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        exit();
    }
    $stmt->bind_param("iiisssss", $_SESSION['user_id'], $service_id, $sub_service_id, $title, $gig_description, $pricing, $tags, $file_path);

    if ($stmt->execute()) {
        echo "Gig successfully created!";
        // Redirect to success page or gig dashboard
      
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- Gig Creation Form -->
<div class="welcome-section">
    <h2>Create New Gig</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="gig-title" class="form-label">Gig Title</label>
                <input type="text" class="form-control" id="gig-title" name="gig-title" required>
            </div>
            
            <div class="mb-3">
                <label for="service_id" class="form-label">Service Category</label>
                <select class="form-select" id="service_id" name="service_id" required>
                    <option value="">Select a Service</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="sub_service_id" class="form-label">Sub Service</label>
                <select class="form-select" id="sub_service_id" name="sub_service_id" required disabled>
                    <option value="">Select a Sub Service</option>
                    <?php foreach ($sub_services as $sub): ?>
                        <option value="<?= $sub['id'] ?>" data-service="<?= $sub['service_id'] ?>">
                            <?= htmlspecialchars($sub['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="gig-description" class="form-label">Description</label>
                <textarea class="form-control" id="gig-description" name="gig-description" rows="4" required></textarea>
            </div>
            
            <div class="mb-3">
                <label for="pricing" class="form-label">Pricing (in USD)</label>
                <input type="number" class="form-control" id="pricing" name="pricing" min="1" step="0.01" required>
            </div>
            
            <div class="mb-3">
                <label for="gig-image" class="form-label">Gig Image</label>
                <input type="file" class="form-control" id="gig-image" name="gig-image" accept="image/*" required>
            </div>
            
            <div class="mb-3">
                <label for="tags" class="form-label">Tags (comma separated)</label>
                <input type="text" class="form-control" id="tags" name="tags" placeholder="e.g., web design, logo, branding">
            </div>
            
            <button type="submit" class="btn btn-primary">Create Gig</button>
        </form>
    </div>
</div>

<script>
document.getElementById('service_id').addEventListener('change', function() {
    const serviceId = this.value;
    const subServiceSelect = document.getElementById('sub_service_id');
    const subServiceOptions = subServiceSelect.getElementsByTagName('option');
    
    // Enable sub-service select
    subServiceSelect.disabled = false;
    
    // Hide all options first
    for (let option of subServiceOptions) {
        if (option.value === "") continue; // Skip the default option
        option.style.display = 'none';
    }
    
    // Show only relevant sub-services
    for (let option of subServiceOptions) {
        if (option.value === "") continue; // Skip the default option
        if (option.dataset.service === serviceId) {
            option.style.display = '';
        }
    }
    
    // Reset sub-service selection
    subServiceSelect.value = "";
});
</script>
