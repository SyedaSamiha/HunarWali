<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>

document.getElementById('password').addEventListener('input', function () {
    const password = this.value;
    const strengthMessage = document.getElementById('passwordStrengthMessage');

    // Use zxcvbn for password strength checking
    const strength = zxcvbn(password);

    if (strength.score < 2) {
        strengthMessage.innerText = "Weak password. Use a mix of letters, numbers, and symbols.";
        strengthMessage.style.color = 'red';
    } else if (strength.score < 3) {
        strengthMessage.innerText = "Medium password. Add more complexity.";
        strengthMessage.style.color = 'orange';
    } else {
        strengthMessage.innerText = "Strong password!";
        strengthMessage.style.color = 'green';
    }
});
