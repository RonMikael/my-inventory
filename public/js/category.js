$(document).ready(function () {
    $('#confirmDeleteCategoryModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var categoryId = button.data('category-id'); // Extract info from data-* attributes
        var modal = $(this);
        modal.find('#deleteCategoryForm').attr('action', '/categories/' + categoryId);
    });

    $('.btn-view-category').click(function (e) {
        e.preventDefault();
        var categoryId = $(this).data('category-id');
        // AJAX request to fetch category details
        $.ajax({
            url: '/categories/' + categoryId + /show/,
            type: 'GET',
            success: function (response) {
                // Populate category details in modal
                $('#categoryName').text(response.name);
                // Show the modal
                $('#categoryDetailsModal').modal('show');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    $('.btn-edit-category').click(function (e) {
        e.preventDefault();
        var categoryId = $(this).data('category-id');
        // AJAX request to fetch category details
        $.ajax({
            url: '/categories/' + categoryId + '/edit',
            type: 'GET',
            success: function (response) {
                // Populate category details in edit modal
                $('#editCategoryName').val(response.name);
                $('#editCategoryForm').attr('action', '/categories/' + categoryId); // Set action URL for form
                // Show the modal
                $('#editCategoryModal').modal('show');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    $('#addCategoryName').on('input', function() {
        var capitalized = $(this).val().toUpperCase();
        $(this).val(capitalized);
    });
});
