// Category data
const categories = {
    domestic: [
        { value: 'cleaning', label: 'Cleaning' },
        { value: 'art-craft', label: 'Art and Craft' },
        { value: 'fashion-textile', label: 'Fashion and Textile' },
        { value: 'beauty-wellness', label: 'Beauty and Wellness' },
        { value: 'culinary-art', label: 'Culinary Art' },
        { value: 'health-care', label: 'Health and Care' },
        { value: 'decorative-art', label: 'Decorative Art' }
    ],
    remote: [
        { value: 'video-animation', label: 'Video and Animation' },
        { value: 'graphic-design', label: 'Graphic Designer' },
        { value: 'digital-marketing', label: 'Digital Marketing' },
        { value: 'writing-content', label: 'Writing And Content Creation' },
        { value: 'online-education', label: 'Online Education' },
        { value: 'web-development', label: 'Web Development' },
        { value: 'app-development', label: 'App Development' }
    ]
};

// Handle primary category change
document.getElementById('primary-category').addEventListener('change', function() {
    const subCategorySelect = document.getElementById('sub-category');
    const selectedCategory = this.value;
    
    // Clear subcategory select
    subCategorySelect.innerHTML = '<option value="">Select Sub Category</option>';
    
    if (selectedCategory) {
        // Enable subcategory select
        subCategorySelect.removeAttribute('disabled');
        
        // Add options based on selected category
        categories[selectedCategory].forEach(category => {
            const option = document.createElement('option');
            option.value = category.value;
            option.textContent = category.label;
            subCategorySelect.appendChild(option);
        });
    } else {
        // Disable subcategory select if no primary category selected
        subCategorySelect.setAttribute('disabled', 'disabled');
    }
});

// Image preview functionality
function previewImage(event) {
    const preview = document.getElementById('preview');
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function() {
        preview.src = reader.result;
        preview.style.display = 'block';
        document.querySelector('.plus-icon').style.display = 'none';
    }

    if (file) {
        reader.readAsDataURL(file);
    }
}

// Form submission validation
document.querySelector("form").addEventListener("submit", function(event) {
    // Prevent form submission to validate first
    event.preventDefault();

    const fileInput = document.getElementById("gig-image");
    const file = fileInput.files[0];
    const primaryCategory = document.getElementById("primary-category").value;
    const subCategory = document.getElementById("sub-category").value;

    // Validate required fields
    const gigTitle = document.getElementById("gig-title").value.trim();
    const gigDescription = document.getElementById("gig-description").value.trim();
    const tags = document.getElementById("tags").value.trim();

    if (!gigTitle || !gigDescription || !tags || !file || !primaryCategory || !subCategory) {
        alert("Please fill in all required fields and upload an image.");
        return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert("File size exceeds 5MB. Please upload a smaller image.");
        return;
    }

    // Validate file type (JPEG/PNG only)
    if (!["image/jpeg", "image/png"].includes(file.type)) {
        alert("Only JPEG or PNG files are allowed!");
        return;
    }

    // If all validations pass, submit the form
    this.submit();
});
