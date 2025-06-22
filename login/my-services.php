<?php
// Include database connection
require_once('../config/database.php');

// Get user's gigs from database
$user_id = $_SESSION['user_id'];
$query = "SELECT 
    g.*, 
    s.name AS service_name, 
    ss.name AS sub_service_name
FROM gigs g
LEFT JOIN services s ON g.service_id = s.id
LEFT JOIN sub_services ss ON g.sub_service_id = ss.id
WHERE g.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Services</h2>
    <a href="dashboard.php?page=gig-creation" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create New Gig
    </a>
</div>

<?php if ($result->num_rows > 0): ?>
    <div class="row">
        <?php while ($gig = $result->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <?php if (!empty($gig['gig_images'])): ?>
                        <img src="../<?php echo htmlspecialchars($gig['gig_images']); ?>" class="card-img-top" alt="Gig Image" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($gig['gig_title']); ?></h5>
                        <p class="card-text text-muted">
                            <small>
                                <i class="fas fa-tag me-1"></i>
                                <?php echo htmlspecialchars($gig['service_name']); ?>
                            </small>
                        </p>
                        <p class="card-text"><?php echo htmlspecialchars(substr($gig['gig_description'], 0, 100)) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">PKR <?php echo number_format($gig['gig_pricing'], 2); ?></span>
                            <div class="btn-group">
                                <a href="dashboard.php?page=gig-edit&id=<?php echo $gig['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteGig(<?php echo $gig['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Created <?php echo date('M d, Y', strtotime($gig['created_at'])); ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-briefcase fa-4x text-muted mb-3"></i>
        <h4>No Services Yet</h4>
        <p class="text-muted">Start by creating your first service!</p>
        <a href="dashboard.php?page=gig-creation" class="btn btn-primary mt-3">
            <i class="fas fa-plus me-2"></i>Create Your First Gig
        </a>
    </div>
<?php endif; ?>

<script>
function deleteGig(gigId) {
    if (confirm('Are you sure you want to delete this gig?')) {
        // Send AJAX request to delete the gig
        fetch('delete-gig.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'gig_id=' + gigId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show updated list
                window.location.reload();
            } else {
                alert('Error deleting gig: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the gig');
        });
    }
}
</script> 