<?php
// Database connection
$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "freelance_website";
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch primary categories (services)
$services = [];
$result = $conn->query("SELECT id, name FROM services WHERE status='active'");
while ($row = $result->fetch_assoc()) {
    $services[] = $row;
}

// Fetch subcategories (sub_services)
$sub_services = [];
$result = $conn->query("SELECT id, service_id, name FROM sub_services WHERE status='active'");
while ($row = $result->fetch_assoc()) {
    $sub_services[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gig Creation | Hunarwali</title>
    <link rel="stylesheet" href="gig.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #fdf1ef;">
    <header>
       <?php include '../navbar/navbar.php'; ?>
    </header>

    <main>
        <section class="gig-section">
            <div class="gig-container" style="background-color: #ffffff;">
                <div class="gig-content">
                    <h1>Create Your Gig</h1>
                    <h2>Share Your Skills with Everyone</h2>

                    <form action="gig.php" method="POST" enctype="multipart/form-data">
                        <div class="form-content">
                            <!-- Gig Image Upload -->
                            <div class="form-group image-upload">
                                <label for="gig-image"><i class="fas fa-image"></i> GIG IMAGE</label>
                                <div class="upload-box" onclick="document.getElementById('gig-image').click();">
                                    <input type="file" id="gig-image" name="gig-image" accept="image/*" onchange="previewImage(event)" required>
                                    <img id="preview" src="" alt="Upload Image">
                                    <span class="plus-icon"><i class="fas fa-plus"></i></span>
                                </div>
                            </div>

                            <div class="form-fields">
                                <!-- Gig Title -->
                                <div class="form-group">
                                    <label for="gig-title"><i class="fas fa-heading"></i> GIG TITLE</label>
                                    <input type="text" id="gig-title" name="gig-title" placeholder="Enter a descriptive title for your gig" required>
                                </div>

                                <!-- Category Selection -->
                                <div class="form-group">
                                    <label><i class="fas fa-list"></i> CATEGORY</label>
                                    <div class="category-selection">
                                        <div class="category-group">
                                            <h4>Primary Category</h4>
                                            <select name="primary-category" id="primary-category" required>
                                                <option value="">Select Primary Category</option>
                                                <?php foreach ($services as $service): ?>
                                                    <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="category-group">
                                            <h4>Sub Category</h4>
                                            <select name="sub-category" id="sub-category" required disabled>
                                                <option value="">Select Sub Category</option>
                                                <?php foreach ($sub_services as $sub): ?>
                                                    <option value="<?= $sub['id'] ?>" data-service="<?= $sub['service_id'] ?>">
                                                        <?= htmlspecialchars($sub['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gig Description -->
                                <div class="form-group">
                                    <label for="gig-description"><i class="fas fa-align-left"></i> GIG DESCRIPTION</label>
                                    <textarea id="gig-description" name="gig-description" placeholder="Describe your services in detail" required></textarea>
                                </div>

                                <!-- Pricing -->
                                <div class="form-group">
                                    <label for="gig-price"><i class="fas fa-tags"></i> PRICING</label>
                                    <div class="pricing-input">
                                        <span class="currency-symbol">PKR</span>
                                        <input type="number" id="gig-price" name="gig-price" min="1" step="0.01" placeholder="Enter your base price" required>
                                    </div>
                                </div>

                                <!-- Tags -->
                                <div class="form-group">
                                    <label for="tags"><i class="fas fa-hashtag"></i> TAGS</label>
                                    <input type="text" id="tags" name="tags" placeholder="Add relevant tags (comma separated)" required>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="button-container">
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-plus-circle"></i> CREATE GIG
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <?php include '../footer/footer.php'; ?>
    </footer>

    <script src="gig.js"></script>
    <script>
    document.getElementById('primary-category').addEventListener('change', function() {
        const subCategorySelect = document.getElementById('sub-category');
        const selectedServiceId = this.value;

        // Hide all subcategories
        Array.from(subCategorySelect.options).forEach(option => {
            if (option.value === "") {
                option.style.display = "";
            } else if (option.getAttribute('data-service') === selectedServiceId) {
                option.style.display = "";
            } else {
                option.style.display = "none";
            }
        });

        // Enable or disable subcategory select
        subCategorySelect.disabled = !selectedServiceId;
        subCategorySelect.value = "";
    });
    </script>
</body>
</html>
