<?php
// Get the current page URL to determine active link
$current_page = $_SERVER['PHP_SELF'];

// Function to get the correct path based on current directory depth
function getBasePath() {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $dir_depth = substr_count($script_name, '/') - 1;
    return str_repeat('../', $dir_depth);
}

$base_path = getBasePath();

// Determine active page
$is_home = strpos($current_page, 'index.php') !== false;
$is_services = strpos($current_page, 'services') !== false;
$is_hire = strpos($current_page, 'hire') !== false;
$is_how_it_works = strpos($current_page, 'how-it-works') !== false;
$is_register = strpos($current_page, 'registration') !== false;
$is_login = strpos($current_page, 'login') !== false;
?>

<link rel="stylesheet" href="/navbar/navbar.css">

<navbar>
    <div class="navbar">
        <div class="nav_logo">
            <div class="logo">
                <a href="<?php echo $base_path; ?>index.php">
                    <img src="<?php echo $base_path; ?>assets/logo.png" alt="Logo">
                </a>
            </div>
        </div>
        <ul class="nav-content">
            <li><a href="<?php echo $base_path; ?>index.php" class="<?php echo $is_home ? 'active' : ''; ?>">HOME</a></li>
            <li><a href="<?php echo $base_path; ?>services/services.php" class="<?php echo $is_services ? 'active' : ''; ?>">SERVICES</a></li>
            <li><a href="<?php echo $base_path; ?>how-it-works/index.php" class="<?php echo $is_how_it_works ? 'active' : ''; ?>">HOW IT WORKS</a></li>
            <li><a href="<?php echo $base_path; ?>hire-freelancer/hire.php" class="<?php echo $is_hire ? 'active' : ''; ?>">HIRE</a></li>

            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <li><a href="<?php echo $base_path; ?>registration/index.php" class="<?php echo $is_register ? 'active' : ''; ?>">REGISTER</a></li>

                <?php endif; ?>    
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="<?php 
                    if ($_SESSION['role'] == 'client') {
                        echo $base_path . 'client-panel/messages.php';
                    } elseif ($_SESSION['role'] == 'freelancer') {
                        echo $base_path . 'login/dashboard.php?page=messages';
                    }
                ?>">MESSAGES</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php 
                        if ($_SESSION['role'] == 'freelancer') {
                            echo $base_path . 'login/dashboard.php';
                        } elseif ($_SESSION['role'] == 'client') {
                            echo $base_path . 'client-panel/index.php';
                        } elseif ($_SESSION['role'] == 'admin') {
                            echo $base_path . 'dashboard/admin.php';
                        }
                    ?>">DASHBOARD</a></li>
                    <?php endif; ?>    
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo $base_path; ?>login/logout.php">LOGOUT</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_path; ?>login/index.php" class="<?php echo $is_login ? 'active' : ''; ?>">LOGIN</a></li>
                    <?php endif; ?>
                </ul>
        <a href="<?php echo $base_path; ?>contactus/contact.php">  
        <button class="nav-content" id="contact-btn">CONTACT US!</button>
        </a>
    </div>
</navbar>