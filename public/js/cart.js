$(document).ready(function () {
    // Initialize original total price
    var originalTotal = parseFloat($('#totalPrice').text().replace('₱', '').replace(',', ''));
    var appliedDiscount = {
        type: $('#discountTypeInput').val(),
        value: parseFloat($('#discountValueInput').val())
    };

    // Set up CSRF token in AJAX headers
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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
        var customerName = $(this).data('name');
        $.ajax({
            url: '/cart/select-customer',
            method: 'POST',
            data: { customer_id: customerId },
            success: function (response) {
                $('#customerModal').modal('hide');
                $('#customerButton').html('<i class="bi bi-person-circle"></i> ' + customerName);
            },
            error: function (xhr) {
                alert('Error selecting customer: ' + xhr.status + ' ' + xhr.statusText);
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
            url: '/cart/select-payment-method',
            method: 'POST',
            data: { payment_method: paymentMethod },
            success: function (response) {
                $('#paymentMethodModal').modal('hide');
                $('#paymentMethodButton').html('<i class="bi bi-credit-card"></i> ' + paymentMethod);
            },
            error: function (xhr) {
                alert('Error selecting payment method: ' + xhr.status + ' ' + xhr.statusText);
            }
        });
    });

    // Handle branch selection
    $('#branchList').on('click', '.branch-item', function (e) {
        e.preventDefault(); // Prevent default anchor behavior
        var branchId = $(this).data('id');
        var branchName = $(this).data('name');
        $.ajax({
            url: '/cart/select-branch',
            method: 'POST',
            data: { branch_id: branchId },
            success: function (response) {
                $('#selectedBranch').text(branchName);
                $('#branchDropdown').dropdown('hide'); // Hide dropdown
            },
            error: function (xhr) {
                alert('Error selecting branch: ' + xhr.status + ' ' + xhr.statusText);
            }
        });
    });

    // Handle freebie selection
    $('#freebieDropdown').on('change', function () {
        var freebieId = $(this).val();
        var freebieName = $('#freebieDropdown option:selected').text();
        $('#freebieInput').val(freebieId);
        $('#selectedFreebie').text('Freebie: ' + freebieName);
    });

    // Handle discount type selection
    $('#discountDropdown').on('change', function () {
        var discountType = $(this).val();
        $('#discountTypeInput').val(discountType);
        if (discountType === 'amount' || discountType === 'percentage') {
            $('#discountValue').removeClass('d-none');
        } else {
            $('#discountValue').addClass('d-none');
            $('#discountValueInput').val(0);
            $('#selectedDiscount').text('Discount: None');
        }
    });

    // Handle discount value input
    $('#discountInput').on('input', function () {
        var discountValue = $(this).val();
        var discountType = $('#discountTypeInput').val();
        
        // Validate percentage value
        if (discountType === 'percentage' && discountValue > 100) {
            discountValue = 100;
            $(this).val(discountValue);
        }
        
        $('#discountValueInput').val(discountValue);
    });

    // Apply discount button click
    $('#applyDiscountButton').on('click', function () {
        appliedDiscount.type = $('#discountTypeInput').val();
        appliedDiscount.value = parseFloat($('#discountValueInput').val());
        updateTotalPrice();
    });

    // Refresh button click
    $('#refreshDiscountButton').on('click', function () {
        $('#discountDropdown').val('none').trigger('change');
        $('#discountInput').val('');
        $('#discountValueInput').val('0');
        appliedDiscount = {
            type: 'none',
            value: 0
        };
        $('#totalPrice').text('₱' + originalTotal.toFixed(2));
        $('#selectedDiscount').text('Discount: None');
    });

    function updateTotalPrice() {
        var total = originalTotal;
        var discountType = appliedDiscount.type;
        var discountValue = appliedDiscount.value;

        if (discountType === 'amount') {
            total -= discountValue;
        } else if (discountType === 'percentage') {
            total -= (total * discountValue / 100);
        }

        // Ensure total does not go below zero
        total = Math.max(total, 0);
        $('#totalPrice').text('₱' + total.toFixed(2));

        // Update discount details
        var discountText = discountType === 'percentage' ? discountValue + '%' : '₱' + discountValue.toFixed(2);
        $('#selectedDiscount').text('Discount: ' + (discountType === 'none' ? 'None' : (discountType.charAt(0).toUpperCase() + discountType.slice(1))) + ' - ' + discountText);
    }
});
