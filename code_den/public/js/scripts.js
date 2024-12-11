const wrapper = document.querySelector('#wrapper');
const loginLink = document.querySelector('.login-link');
const registerLink = document.querySelector('.register-link');
const registerForm = document.querySelector('.register form'); // Get the registration form element

registerLink.addEventListener('click', () => { // Register link
    wrapper.classList.add('active');
});

loginLink.addEventListener('click', () => { // Login link 
    wrapper.classList.remove('active');
});

function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
    } else {
        field.type = "password";
    }
}

// Check if passwords match
function checkPasswordsMatch() {
    const password = document.getElementById('register_password').value; // Ensure this matches the ID
    const confirmPassword = document.getElementById('confirm_password').value; // Ensure this matches the ID
    const notification = document.getElementById('notification');

    if (password !== confirmPassword) {
        notification.style.display = 'block';
        notification.style.color = 'red';
        notification.textContent = 'Passwords do not match!';
        return false; // Return false to indicate validation failure
    } else {
        notification.style.display = 'none';
        return true; // Return true to indicate validation success
    }
}

// Validate the form on submit
registerForm.addEventListener('submit', (event) => {
    if (!checkPasswordsMatch()) {
        event.preventDefault(); // Prevent form submission if passwords do not match
    }
});

// Function to validate password strength
function validatePasswordStrength(password) {
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/; // At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special character

    if (!regex.test(password)) {
        alert('Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.');
        return false; // Password does not meet criteria
    } else {
        return true; // Password meets criteria
    }
}

// Validate the form on submit
registerForm.addEventListener('submit', (event) => {
    const password = document.getElementById('register_password').value; // Get the password value
    if (!checkPasswordsMatch() || !validatePasswordStrength(password)) {
        event.preventDefault(); // Prevent form submission if passwords do not match or password is weak
    }
});

// script.js

if ('serviceWorker' in navigator) {
    navigator.serviceWorker
      .register('/sw.js')
      .then((registration) => {
        console.log("Service Worker registered with scope:", registration.scope);
      })
      .catch((error) => {
        console.error("Service Worker registration failed:", error);
      });
  }
  