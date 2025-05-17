document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();  // Prevent form submission to validate first

    const email = document.getElementById('email').value;  // Access email by its id
    const password = document.getElementById('password').value;  // Access password by its id

    // Check if the fields are empty
    if (email.trim() === "" || password.trim() === "") {
        alert("Please enter both email and password.");
        return;  // Prevent form submission if fields are empty
    }

    // Basic email format validation
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(email)) {
        alert("Please enter a valid email.");
        return;  // Prevent form submission if the email is invalid
    }

    // Since we don't need to check against hardcoded credentials here,
    // we simply allow the form to submit if validation passes
    this.submit();  // Submit the form after validation is successful
});
