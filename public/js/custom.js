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
            // Concatenate only the brand, category, and size into one string
            var productBrand = $(this).find('.card-title').text().toLowerCase();
            var productCategory = $(this).find('.card-text:contains("Category")').text().toLowerCase();
            var productSize = $(this).find('.card-text:contains("Size")').text().toLowerCase();
            
            // Combine brand, category, and size into one searchable string
            var productDetails = productBrand + ' ' + productCategory + ' ' + productSize;
    
            // Show or hide the product based on search text
            if (productDetails.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });    

    $('#btn-list-viewproduct').click(function () {
        $('#list-viewproduct').removeClass('d-none');
        $('#kanban-viewproduct').addClass('d-none');
    });
    
    $('#btn-kanban-viewproduct').click(function () {
        $('#kanban-viewproduct').removeClass('d-none');
        $('#list-viewproduct').addClass('d-none');
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
        sortField: 'text', // Sort dropdown items by text
        onType: function(str) {
            this.setTextboxValue(str.toUpperCase());
        }
    });

    $('#brand').selectize({
        create: true, // Allow creating new items
        sortField: 'text', // Sort dropdown items by text
        onType: function(str) {
            this.setTextboxValue(str.toUpperCase());
        }
    });

    $('#reference_number').on('input', function() {
        var capitalized = $(this).val().toUpperCase();
        $(this).val(capitalized);
    });

    $('#editReferenceNumber').on('input', function() {
        var capitalized = $(this).val().toUpperCase();
        $(this).val(capitalized);
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
                    <option value="BUILDING 2">BUILDING 2</option>
                    <option value="DISPLAY">DISPLAY</option>
                </select>
                <label for="location_${stockCount}" class="form-label">Location</label>
                <select id="location_${stockCount}" class="form-control location-select" name="stocks[${stockCount}][location]" required>
                    <option value="">Select Location</option>

                                        <option value="HANGOVER" data-stock-room="BUILDING 2">HANGOVER</option>
                                        <option value="RACK 9" data-stock-room="BUILDING 2">RACK 9</option>
                                        <option value="RACK 1" data-stock-room="BUILDING 2">RACK 1</option>
                                        <option value="RACK 2" data-stock-room="BUILDING 2">RACK 2</option>
                                        <option value="RACK 3" data-stock-room="BUILDING 2">RACK 3</option>

                                        <option value="RACK 1" data-stock-room="DISPLAY">RACK 1</option>
                                        <option value="RACK 2" data-stock-room="DISPLAY">RACK 2</option>
                                        <option value="RACK 3" data-stock-room="DISPLAY">RACK 3</option>
                                        <option value="RACK 4" data-stock-room="DISPLAY">RACK 4</option>

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
            sortField: 'text',
            onType: function(str) {
                this.setTextboxValue(str.toUpperCase());
            }, // Sort dropdown items by text
            onChange: function(value) {
                var locationSelectize = locationSelect[0].selectize;
                var selectedStockRoom = value;
                locationSelectize.clearOptions(); // Clear existing options
                if (selectedStockRoom === 'BUILDING 2') {
                    locationSelectize.addOption({ value: 'HANGOVER', text: 'HANGOVER' });
                    locationSelectize.addOption({ value: 'RACK 9', text: 'RACK 9' });
                    locationSelectize.addOption({ value: 'RACK 1', text: 'RACK 1' });
                    locationSelectize.addOption({ value: 'RACK 2', text: 'RACK 2' });
                    locationSelectize.addOption({ value: 'RACK 3', text: 'RACK 3' });
                } else if (selectedStockRoom === 'DISPLAY') {
                    locationSelectize.addOption({ value: 'RACK 1', text: 'RACK 1' });
                    locationSelectize.addOption({ value: 'RACK 2', text: 'RACK 2' });
                    locationSelectize.addOption({ value: 'RACK 3', text: 'RACK 3' });
                    locationSelectize.addOption({ value: 'RACK 4', text: 'RACK 4' });
                } else {
                }
                locationSelectize.refreshOptions();
            }
        });

        locationSelect.selectize({
            create: true, // Allow creating new items
            sortField: 'text', // Sort dropdown items by text
            onType: function(str) {
                this.setTextboxValue(str.toUpperCase());
            }
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
        sortField: 'text',
        onType: function(str) {
            this.setTextboxValue(str.toUpperCase());
        } // Sort dropdown items by text
    });

    // Initialize selectize for brand
    var $editBrand = $('#editBrand').selectize({
        create: true, // Allow creating new items
        sortField: 'text',
        onType: function(str) {
            this.setTextboxValue(str.toUpperCase());
        } // Sort dropdown items by text
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
                        <option value="BUILDING 2" ${stock.stock_room === 'BUILDING 2' ? 'selected' : ''}>BUILDING 2</option>
                        <option value="DISPLAY" ${stock.stock_room === 'DISPLAY' ? 'selected' : ''}>DISPLAY</option>
                        ${stock.stock_room && !['BUILDING 2', 'DISPLAY'].includes(stock.stock_room) ? `<option value="${stock.stock_room}" selected>${stock.stock_room}</option>` : ''}
                    </select>
                </div>
                <div class="mb-3">
                    <label for="editLocation${editStockIndex}" class="form-label">Location</label>
                    <select id="editLocation${editStockIndex}" class="form-control edit-location-select" name="stocks[${editStockIndex}][location]">
                        <option value="">Select Location</option>
                        ${generateLocationOptions(stock.stock_room, stock.location)}
                        ${stock.location && !['HANGOVER', 'RACK 9', 'RACK 1', 'RACK 2', 'RACK 3', 'RACK 1', 'RACK 2', 'RACK 3', 'RACK 4'].includes(stock.location) ? `<option value="${stock.location}" selected>${stock.location}</option>` : ''}
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
            allowEmptyOption: true,
            onType: function(str) {
                this.setTextboxValue(str.toUpperCase());
            }, // Allow empty option for custom input
            onChange: function (value) {
                const locationSelectize = locationSelect[0].selectize;
                locationSelectize.clearOptions(); // Clear existing options
                if (value === 'BUILDING 2') {
                    locationSelectize.addOption({ value: 'HANGOVER', text: 'HANGOVER' });
                    locationSelectize.addOption({ value: 'RACK 9', text: 'RACK 9' });
                    locationSelectize.addOption({ value: 'RACK 1', text: 'RACK 1' });
                    locationSelectize.addOption({ value: 'RACK 2', text: 'RACK 2' });
                    locationSelectize.addOption({ value: 'RACK 3', text: 'RACK 3' });
                } else if (value === 'DISPLAY') {
                    locationSelectize.addOption({ value: 'RACK 1', text: 'RACK 1' });
                    locationSelectize.addOption({ value: 'RACK 2', text: 'RACK 2' });
                    locationSelectize.addOption({ value: 'RACK 3', text: 'RACK 3' });
                    locationSelectize.addOption({ value: 'RACK 4', text: 'RACK 4' });
                }
                locationSelectize.refreshOptions();
            }
        });

        locationSelect.selectize({
            create: true, // Allow creating new items
            sortField: 'text',
            onType: function(str) {
                this.setTextboxValue(str.toUpperCase());
            } // Sort dropdown items by text
        });
    }  
    
    function generateLocationOptions(stockRoom, selectedLocation) {
        let optionsHtml = '';
        if (stockRoom === 'BUILDING 2') {
            optionsHtml += `<option value="HANGOVER" ${selectedLocation === 'HANGOVER' ? 'selected' : ''}>HANGOVER</option>`;
            optionsHtml += `<option value="RACK 9" ${selectedLocation === 'RACK 9' ? 'selected' : ''}>RACK 9</option>`;
            optionsHtml += `<option value="RACK 1" ${selectedLocation === 'RACK 1' ? 'selected' : ''}>RACK 1</option>`;
            optionsHtml += `<option value="RACK 2" ${selectedLocation === 'RACK 2' ? 'selected' : ''}>RACK 2</option>`;
            optionsHtml += `<option value="RACK 3" ${selectedLocation === 'RACK 3' ? 'selected' : ''}>RACK 3</option>`;
        } else if (stockRoom === 'DISPLAY') {
            optionsHtml += `<option value="RACK 1" ${selectedLocation === 'RACK 1' ? 'selected' : ''}>RACK 1</option>`;
            optionsHtml += `<option value="RACK 2" ${selectedLocation === 'RACK 2' ? 'selected' : ''}>RACK 2</option>`;
            optionsHtml += `<option value="RACK 3" ${selectedLocation === 'RACK 3' ? 'selected' : ''}>RACK 3</option>`;
            optionsHtml += `<option value="RACK 4" ${selectedLocation === 'RACK 4' ? 'selected' : ''}>RACK 4</option>`;
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
