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
});

$(document).ready(function() {
    $('.delete-button').click(function() {
        var codeId = $(this).data('code-id');

        if (confirm("Are you sure you want to delete this saved code?")) {
            $.ajax({
                url: 'backend/delete_saved_code.php',
                type: 'POST',
                data: {
                    id: codeId
                },
                success: function(response) {
                    if (response === 'Code deleted successfully.') {
                        location.reload();
                    } else {
                        alert('Error: ' + response);
                    }
                }
            });
        }
    });
});