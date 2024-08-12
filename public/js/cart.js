$(document).ready(function () {
    // Display the quantity limit modal if there's an error
    if ($('#quantityLimitMessage').text().trim() !== '') {
        $('#quantityLimitModal').show();
    }

    // Handle clicks on close buttons or outside the modal to hide it
    $(document).on('click', '#quantityLimitModal .btn-close, #quantityLimitModal .btn-secondary', function () {
        $('#quantityLimitModal').hide();
    });

    $(window).on('click', function (event) {
        if ($(event.target).is('#quantityLimitModal')) {
            $('#quantityLimitModal').hide();
        }
    });

    // Search functionality for customers
    $('#customerSearch').on('keyup', function () {
        var query = $(this).val().toLowerCase();
        $('#customerList .list-group-item').each(function () {
            var name = $(this).text().toLowerCase();
            $(this).toggle(name.includes(query));
        });
    });

    // Handle customer selection
    $('#customerList').on('click', '.customer-item', function () {
        var customerId = $(this).data('id');
        $.ajax({
            url: '{{ route("cart.selectCustomer") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                customer_id: customerId
            },
            success: function (response) {
                $('#customerModal').hide();
                location.reload(); // Optionally reload the page to reflect changes
            },
            error: function () {
                alert('Error selecting customer');
            }
        });
    });

    // Search functionality for payment methods
    $('#paymentSearch').on('keyup', function () {
        var query = $(this).val().toLowerCase();
        $('#paymentList .list-group-item').each(function () {
            var method = $(this).text().toLowerCase();
            $(this).toggle(method.includes(query));
        });
    });

    // Handle payment method selection
    $('#paymentList').on('click', '.payment-item', function () {
        var paymentMethod = $(this).data('method');
        $.ajax({
            url: '{{ route("cart.selectPaymentMethod") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                payment_method: paymentMethod
            },
            success: function (response) {
                $('#paymentMethodModal').hide();
                location.reload(); // Optionally reload the page to reflect changes
            },
            error: function () {
                alert('Error selecting payment method');
            }
        });
    });
});
