document.getElementById('profile-upload').addEventListener('change', function(event) {
    const preview = document.getElementById('profile-preview');
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
        }

        reader.readAsDataURL(file);
    } else {
        preview.src = "profile.png";  // Default image
    }
});

document.querySelector("form").addEventListener("submit", function(event) {
    const gender = "<?php echo $_SESSION['gender']; ?>"; // Fetch gender from session

    if (gender !== "female") {
        event.preventDefault();
        alert("Only female users can register as a freelancer.");
        document.querySelector("input[type='file']").style.border = "1px solid red";
    }
});
