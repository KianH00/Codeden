
$(document).ready(function() {
    $('.delete-button').click(function() {
        var codeId = $(this).data('code-id');

        if (confirm("Are you sure you want to delete this saved code?")) {
            $.ajax({
                url: 'delete_saved_code.php',
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