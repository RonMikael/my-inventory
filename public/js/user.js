$(document).ready(function () {
    //USER CRUD START//
    $('#confirmDeleteUserModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var userId = button.data('user-id'); // Extract info from data-* attributes
        var modal = $(this);
        modal.find('#deleteUserForm').attr('action', '/users/' + userId);
    });

    $('.btn-view-user').click(function (e) {
        e.preventDefault();
        var userId = $(this).data('user-id');
        // AJAX request to fetch user details
        $.ajax({
            url: '/users/' + userId + '/show',
            type: 'GET',
            success: function (response) {
                // Populate user details in modal
                $('#userName').text(response.name);
                $('#userEmail').text(response.email);
                $('#userRole').text(response.role);
                // Show the modal
                $('#userDetailsModal').modal('show');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    $('.btn-edit-user').click(function (e) {
        e.preventDefault();
        var userId = $(this).data('user-id');
        // AJAX request to fetch user details
        $.ajax({
            url: '/users/' + userId + '/edit',
            type: 'GET',
            success: function (response) {
                // Populate user details in edit modal
                $('#editUserName').val(response.name);
                $('#editUserEmail').val(response.email);
                $('#editUserForm').attr('action', '/users/' + userId); // Set action URL for form
                // Show the modal
                $('#editUserModal').modal('show');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    $('.btn-edit-user-role').click(function (e) {
        e.preventDefault();
        var userId = $(this).data('user-id');
        // AJAX request to fetch user details
        $.ajax({
            url: '/users/' + userId + '/edit',
            type: 'GET',
            success: function (response) {
                // Populate user details in edit modal
                $('#editUserRole').val(response.role);
                $('#editUserRoleForm').attr('action', '/users/' + userId + '/role'); // Set action URL for form
                // Show the modal
                $('#editUserRoleModal').modal('show');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    $('#exportUserButton').click(function (e) {
        e.preventDefault();
        window.location.href = '/users/export';
    });

    $('#searchInputUser').on('input', function () {
        var searchText = $(this).val().toLowerCase();
        $('.table tbody tr').each(function () {
            var name = $(this).find('td:eq(0)').text().toLowerCase();
            var email = $(this).find('td:eq(1)').text().toLowerCase();
            var role = $(this).find('td:eq(2)').text().toLowerCase();
            if (name.includes(searchText) || email.includes(searchText) || role.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $('.filter-option').click(function(e) {
        e.preventDefault();
        var role = $(this).data('role');
        $('.table tbody tr').each(function() {
            var userRole = $(this).find('td:eq(1)').text().toLowerCase();
            if (role === '' || userRole === role) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Eye toggle functionality for password field
    $('#eye-toggle').click(function () {
        var passwordInput = $('#password');
        var eyeIcon = $(this).find('i');
        if (passwordInput.attr('type') === "password") {
            passwordInput.attr('type', 'text');
            eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Eye toggle functionality for confirm password field
    $('#confirm-password-eye-toggle').click(function () {
        var confirmPasswordInput = $('#password-confirm'); // Correctly targeting the confirm password input field
        var eyeIcon = $(this).find('i');
        if (confirmPasswordInput.attr('type') === "password") {
            confirmPasswordInput.attr('type', 'text');
            eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            confirmPasswordInput.attr('type', 'password');
            eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    //USER CRUD END//
});