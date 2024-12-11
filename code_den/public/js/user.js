const hamBurger = document.querySelector(".toggle-btn");

hamBurger.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("expand");
});

document.addEventListener('DOMContentLoaded', function() {
  const copyButtons = document.querySelectorAll('.copy-button');

  copyButtons.forEach(button => {
      button.addEventListener('click', function() {
          const codeId = this.getAttribute('data-code-id');
          const codeElement = document.getElementById('code-' + codeId);
          
          // Create a temporary textarea to hold the code
          const textarea = document.createElement('textarea');
          textarea.value = codeElement.textContent; // Get the code text
          document.body.appendChild(textarea);
          textarea.select(); // Select the text
          document.execCommand('copy'); // Copy the text to clipboard
          document.body.removeChild(textarea); // Remove the textarea
      });
  });

  // Add event listeners for the saved buttons
  const savedButtons = document.querySelectorAll('.saved-button');
  savedButtons.forEach(button => {
      button.addEventListener('click', function() {
          console.log('Form submitted for code ID:', this.previousElementSibling.value); // Log the code_id
      });
  });
});

document.querySelectorAll('.language-button').forEach(button => {
  button.addEventListener('click', function() {
      const language = this.getAttribute('data-language');
      window.location.href = `homep.php?category=${language}`;
  });
});