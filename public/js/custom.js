$(document).ready(function () {
    // Hide success message after 2 seconds
    setTimeout(function () {
        $('.alert-success').fadeOut('slow');
    }, 2000);

    // Hide success message after 2 seconds
    setTimeout(function () {
        $('.alert-danger').fadeOut('slow');
    }, 2000);

    $('#searchInputProduct').on('input', function () {
        var searchText = $(this).val().toLowerCase();
        $('.product-item').each(function () {
            var productName = $(this).find('.card-title').text().toLowerCase();
            var categoryName = $(this).find('.card-text').first().text().toLowerCase();
            if (productName.includes(searchText) || categoryName.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    //PRODUCT CRUD START//
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInputProductIndex');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const searchText = this.value.trim().toLowerCase(); // Trim whitespace and convert to lowercase
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    let rowVisible = false; // Flag to determine if row should be visible
                    row.querySelectorAll('td').forEach(cell => {
                        // Check if any cell contains the search text
                        if (cell.textContent.trim().toLowerCase().includes(searchText)) {
                            rowVisible = true;
                        }
                    });
                    // Toggle row visibility based on search text
                    row.style.display = rowVisible ? '' : 'none';
                });
            });
        }
    });        

    $('#size').selectize({
        create: true, // Allow creating new items
        sortField: 'text' // Sort dropdown items by text
    });

    $('#brand').selectize({
        create: true, // Allow creating new items
        sortField: 'text' // Sort dropdown items by text
    });

    function updateLocationOptions(stockRoomSelect, locationSelect) {
        var selectedStockRoom = stockRoomSelect.val();
        locationSelect.find('option').each(function () {
            var option = $(this);
            if (option.data('stock-room') === selectedStockRoom || option.data('stock-room') === undefined) {
                option.show();
            } else {
                option.hide();
            }
        });
        locationSelect.val(''); // Reset selected location
    }

    // Initial update of location options
    updateLocationOptions($('#stock_room'), $('#location'));

    // Event listener for adding another stock
    $('#addStockButton').click(function () {
        // Get the number of existing stock items to set unique indices
        var stockCount = $('#stockContainer .stock-item').length;

        // HTML template for new stock item with unique indices
        var newStockItemHTML = `
            <div class="stock-item mb-3">
                <label for="stock_room_${stockCount}" class="form-label">Stock Room</label>
                <select id="stock_room_${stockCount}" class="form-control stock-room-select" name="stocks[${stockCount}][stock_room]" required>
                    <option value="">Select Stock Room</option>
                    <option value="Building 1">Building 1</option>
                    <option value="Building 2">Building 2</option>
                </select>
                <label for="location_${stockCount}" class="form-label">Location</label>
                <select id="location_${stockCount}" class="form-control location-select" name="stocks[${stockCount}][location]" required>
                    <option value="">Select Location</option>
                    <option value="Rack 1" data-stock-room="Building 1">Rack 1</option>
                    <option value="Rack 2" data-stock-room="Building 1">Rack 2</option>
                    <option value="Rack A" data-stock-room="Building 2">Rack A</option>
                    <option value="Rack B" data-stock-room="Building 2">Rack B</option>
                </select>
                <label for="quantity_${stockCount}" class="form-label">Quantity</label>
                <input type="number" id="quantity_${stockCount}" class="form-control" name="stocks[${stockCount}][quantity]" required>
            </div>
        `;

        // Append new stock item HTML to stock container
        $('#stockContainer').append(newStockItemHTML);

        // Initialize Selectize for the new stock room and location dropdowns
        initSelectize($(`#stock_room_${stockCount}`), $(`#location_${stockCount}`));
    });

    function initSelectize(stockRoomSelect, locationSelect) {
        stockRoomSelect.selectize({
            create: true, // Allow creating new items
            sortField: 'text', // Sort dropdown items by text
            onChange: function(value) {
                var locationSelectize = locationSelect[0].selectize;
                var selectedStockRoom = value;
                locationSelectize.clearOptions(); // Clear existing options
                if (selectedStockRoom === 'Building 1') {
                    locationSelectize.addOption({ value: 'Rack 1', text: 'Rack 1' });
                    locationSelectize.addOption({ value: 'Rack 2', text: 'Rack 2' });
                } else if (selectedStockRoom === 'Building 2') {
                    locationSelectize.addOption({ value: 'Rack A', text: 'Rack A' });
                    locationSelectize.addOption({ value: 'Rack B', text: 'Rack B' });
                } else {
                }
                locationSelectize.refreshOptions();
            }
        });

        locationSelect.selectize({
            create: true, // Allow creating new items
            sortField: 'text' // Sort dropdown items by text
        });
    }

    // Initialize Selectize for the initial stock room and location dropdowns
    initSelectize($('#stock_room'), $('#location'));

    $('#confirmDeleteProductModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var productId = button.data('product-id'); // Extract product ID from data attributes
        var form = $(this).find('#deleteProductForm'); // Find the delete form within the modal
        // Set the action attribute of the form with the correct product ID
        form.attr('action', '/products/' + productId);
    });

    const editProductModal = $('#editProductModal');
    const editProductForm = $('#editProductForm');
    const editStockContainer = $('#editStockContainer');
    const addEditStockButton = $('#addEditStockButton');
    let editStockIndex = 0;

    var $editSize = $('#editSize').selectize({
        create: true, // Allow creating new items
        sortField: 'text' // Sort dropdown items by text
    });

    // Initialize selectize for brand
    var $editBrand = $('#editBrand').selectize({
        create: true, // Allow creating new items
        sortField: 'text' // Sort dropdown items by text
    });

    $('.btn-edit-product').click(function () {
        const productId = $(this).data('product-id');
        fetch(`/products/${productId}/edit`)
            .then(response => response.json())
            .then(data => {
                const product = data.product;
                $('#editProductId').val(product.id);

                // Get selectize instances
                var sizeSelectize = $editSize[0].selectize;
                var brandSelectize = $editBrand[0].selectize;

                // Check if the size is in the options, if not, add it
                if (!sizeSelectize.options.hasOwnProperty(product.size)) {
                    sizeSelectize.addOption({value: product.size, text: product.size});
                }

                // Set the value of the size selectize control
                sizeSelectize.setValue(product.size);

                // Check if the brand is in the options, if not, add it
                if (!brandSelectize.options.hasOwnProperty(product.brand)) {
                    brandSelectize.addOption({value: product.brand, text: product.brand});
                }

                // Set the value of the brand selectize control
                brandSelectize.setValue(product.brand);

                $('#editReferenceNumber').val(product.reference_number);
                $('#editPrice').val(product.price);
                $('#editCategoryId').val(product.category_id);

                // Populate stocks
                editStockContainer.empty();
                editStockIndex = 0;
                product.stocks.forEach(stock => {
                    addEditStock(stock);
                });

                // Populate images
                const existingImages = $('#existingImages');
                existingImages.empty();
                product.images.forEach(image => {
                    const imgElement = $('<img>').attr('src', `/storage/${image.image_path}`).attr('width', 50).attr('height', 50);
                    existingImages.append(imgElement);

                    const deleteImageCheckbox = $('<input>').attr('type', 'checkbox').attr('name', 'deleted_images[]').val(image.id);
                    const deleteImageLabel = $('<label>').text('Delete');
                    existingImages.append(deleteImageCheckbox).append(deleteImageLabel);
                });

                editProductForm.attr('action', `/products/${product.id}`);
                editProductModal.modal('show');
            });
    });

    addEditStockButton.click(() => {
        addEditStock();
    });

    function addEditStock(stock = {}) {
        const stockHtml = `
            <div class="stock-item mb-3" data-stock-index="${editStockIndex}">
                <div class="mb-3">
                    <label for="editStockRoom${editStockIndex}" class="form-label">Stock Room</label>
                    <select id="editStockRoom${editStockIndex}" class="form-control edit-stock-room-select" name="stocks[${editStockIndex}][stock_room]">
                        <option value="">Select Stock Room</option>
                        <option value="Building 1" ${stock.stock_room === 'Building 1' ? 'selected' : ''}>Building 1</option>
                        <option value="Building 2" ${stock.stock_room === 'Building 2' ? 'selected' : ''}>Building 2</option>
                        ${stock.stock_room && !['Building 1', 'Building 2'].includes(stock.stock_room) ? `<option value="${stock.stock_room}" selected>${stock.stock_room}</option>` : ''}
                    </select>
                </div>
                <div class="mb-3">
                    <label for="editLocation${editStockIndex}" class="form-label">Location</label>
                    <select id="editLocation${editStockIndex}" class="form-control edit-location-select" name="stocks[${editStockIndex}][location]">
                        <option value="">Select Location</option>
                        ${generateLocationOptions(stock.stock_room, stock.location)}
                        ${stock.location && !['Rack 1', 'Rack 2', 'Rack A', 'Rack B'].includes(stock.location) ? `<option value="${stock.location}" selected>${stock.location}</option>` : ''}
                    </select>
                </div>
                <label for="editQuantity${editStockIndex}" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="editQuantity${editStockIndex}" name="stocks[${editStockIndex}][quantity]" value="${stock.quantity || ''}">
                ${stock.id ? `<input type="hidden" name="stocks[${editStockIndex}][id]" value="${stock.id}">` : ''}
                <button type="button" class="btn btn-danger btn-sm mt-2" data-stock-id="${stock.id}" onclick="removeEditStock(${editStockIndex})">Remove</button>
            </div>
        `;
        editStockContainer.append(stockHtml);
    
        // Initialize Selectize for the new stock room and location selects
        const editStockRoomSelect = $(`#editStockRoom${editStockIndex}`);
        const editLocationSelect = $(`#editLocation${editStockIndex}`);
        initEditSelectize(editStockRoomSelect, editLocationSelect);
        
        // Set the value of the stock room selectize control
        editStockRoomSelect[0].selectize.setValue(stock.stock_room);
    
        editStockIndex++;
    }       

    function initEditSelectize(stockRoomSelect, locationSelect) {
        stockRoomSelect.selectize({
            create: true, // Allow creating new items
            sortField: 'text', // Sort dropdown items by text
            allowEmptyOption: true, // Allow empty option for custom input
            onChange: function (value) {
                const locationSelectize = locationSelect[0].selectize;
                locationSelectize.clearOptions(); // Clear existing options
                if (value === 'Building 1') {
                    locationSelectize.addOption({ value: 'Rack 1', text: 'Rack 1' });
                    locationSelectize.addOption({ value: 'Rack 2', text: 'Rack 2' });
                } else if (value === 'Building 2') {
                    locationSelectize.addOption({ value: 'Rack A', text: 'Rack A' });
                    locationSelectize.addOption({ value: 'Rack B', text: 'Rack B' });
                }
                locationSelectize.refreshOptions();
            }
        });

        locationSelect.selectize({
            create: true, // Allow creating new items
            sortField: 'text' // Sort dropdown items by text
        });
    }  
    
    function generateLocationOptions(stockRoom, selectedLocation) {
        let optionsHtml = '';
        if (stockRoom === 'Building 1') {
            optionsHtml += `<option value="Rack 1" ${selectedLocation === 'Rack 1' ? 'selected' : ''}>Rack 1</option>`;
            optionsHtml += `<option value="Rack 2" ${selectedLocation === 'Rack 2' ? 'selected' : ''}>Rack 2</option>`;
        } else if (stockRoom === 'Building 2') {
            optionsHtml += `<option value="Rack A" ${selectedLocation === 'Rack A' ? 'selected' : ''}>Rack A</option>`;
            optionsHtml += `<option value="Rack B" ${selectedLocation === 'Rack B' ? 'selected' : ''}>Rack B</option>`;
        } else {
        }
        return optionsHtml;
    }    

    window.removeEditStock = function(editStockIndex) {
        // Get the stock ID from the button's data attribute
        var stockId = $(`#editStockRoom${editStockIndex}`).closest('.stock-item').find('button').data('stock-id');
        
        // Check if the stock ID is valid
        if (stockId) {
            // Set the data-stock-id attribute for the modal confirmation button
            $('#confirmDeleteStockButton').data('stock-id', stockId);
    
            // Show the confirmation modal
            $('#confirmDeleteStockModal').modal('show');
        } else {
            console.error('Stock ID not found for index:', editStockIndex);
        }
    }    
    
// Handle delete button click
    $('.btn-delete-stock').click(function () {
        var stockId = $(this).data('stock-id');
        $('#confirmDeleteStockButton').data('stock-id', stockId); // Set stockId to modal button
        $('#confirmDeleteStockModal').modal('show');
    });

    $('#confirmDeleteStockButton').click(function () {
        var stockId = $(this).data('stock-id'); // Get the stock ID from the button data attribute
        var token = $('meta[name="csrf-token"]').attr('content');
    
        $.ajax({
            url: `/stocks/${stockId}`, // Make sure this URL matches your route definition
            type: 'DELETE',
            data: {
                _token: token // Include the CSRF token
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Remove the stock item from the UI
                    $(`button[data-stock-id="${stockId}"]`).closest('.stock-item').remove();
                    $('#confirmDeleteStockModal').modal('hide'); // Hide the confirmation modal
                } else {
                    alert('Failed to delete stock.'); // Show an alert on failure
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                // Handle error if needed
            }
        });
    });
           
    //PRODUCT CRUD END//
});
