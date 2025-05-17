document.getElementById('newsletter-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var email = document.getElementById('email').value;
    var messageDiv = document.getElementById('message');

    // Basic email validation
    var emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
    if (!emailPattern.test(email)) {
        messageDiv.style.color = 'red';
        messageDiv.textContent = 'Please enter a valid email address.';
        return;
    }

    // AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'subscribe.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            messageDiv.style.color = 'green';
            messageDiv.textContent = xhr.responseText;
            document.getElementById('newsletter-form').reset();
        } else {
            messageDiv.style.color = 'red';
            messageDiv.textContent = 'An error occurred. Please try again.';
        }
    };
    xhr.send('email=' + encodeURIComponent(email));
});



//How It Works
/*Front-end: Users enter their email and click "Subscribe". The form is validated in JS and sent via AJAX to the server.

Back-end: The PHP script receives the email, validates it, and saves it to emails.txt (avoiding duplicates).

Files: All files are separate and easy to maintain.

Note: For production, use a database and add CSRF protection, input sanitization, and error handling.*/