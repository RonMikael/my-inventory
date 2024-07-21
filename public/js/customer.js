$(document).ready(function () {
//CUSTOMER CRUD START//
$('#btn-list-view').click(function () {
    $('#list-view').removeClass('d-none');
    $('#kanban-view').addClass('d-none');
});

$('#btn-kanban-view').click(function () {
    $('#kanban-view').removeClass('d-none');
    $('#list-view').addClass('d-none');
});

$('#confirmDeleteCustomerModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var customerId = button.data('customer-id'); // Extract info from data-* attributes
    var modal = $(this);
    modal.find('#deleteCustomerForm').attr('action', '/customers/' + customerId);
});

$('.btn-view-customer').click(function (e) {
    e.preventDefault();
    var customerId = $(this).data('customer-id');
    // AJAX request to fetch user details
    $.ajax({
        url: '/customers/' + customerId + '/show',
        type: 'GET',
        success: function (response) {
            // Populate user details in modal
            $('#customerName').text(response.name);
            $('#customerEmail').text(response.email);
            $('#customerPhone').text(response.phone);
            $('#customerAddress').text(response.address);
            // Show the modal
            $('#customerDetailsModal').modal('show');
        },
        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
});

$('.btn-edit-customer').click(function (e) {
    e.preventDefault();
    var customerId = $(this).data('customer-id');
    // AJAX request to fetch user details
    $.ajax({
        url: '/customers/' + customerId + '/edit',
        type: 'GET',
        success: function (response) {
            // Populate user details in edit modal
            $('#editCustomerName').val(response.name);
            $('#editCustomerEmail').val(response.email);
            $('#editCustomerPhone').val(response.phone);
            $('#editCustomerAddress').val(response.address);
            $('#editCustomerForm').attr('action', '/customers/' + customerId); // Set action URL for form
            // Show the modal
            $('#editCustomerModal').modal('show');
        },
        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
});

$('#searchInputCustomer').on('input', function () {
    var searchText = $(this).val().toLowerCase();
    $('.table tbody tr').each(function () {
        var name = $(this).find('td:eq(0)').text().toLowerCase();
        var email = $(this).find('td:eq(1)').text().toLowerCase();
        var phone = $(this).find('td:eq(2)').text().toLowerCase();
        var address = $(this).find('td:eq(3)').text().toLowerCase();
        if (name.includes(searchText) || email.includes(searchText) || phone.includes(searchText) || address.includes(searchText)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});
//CUSTOMER CRUD END//
});