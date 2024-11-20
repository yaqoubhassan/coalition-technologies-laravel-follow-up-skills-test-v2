<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Product Form</h2>
        <form id="productForm" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="product_name" placeholder="Product Name" required>
                </div>
                <div class="col-md-4">
                    <input type="number" class="form-control" name="quantity_in_stock" placeholder="Quantity in Stock"
                        required>
                </div>
                <div class="col-md-4">
                    <input type="number" step="0.01" class="form-control" name="price_per_item"
                        placeholder="Price per Item" required>
                </div>
            </div>
            <div class="text-end"><button type="submit" class="btn btn-primary mt-3">Submit</button></div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity in Stock</th>
                    <th>Price per Item</th>
                    <th>Date Submitted</th>
                    <th>Totale Value</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTable">
                @php $sumTotal = 0; @endphp
                @foreach ($data as $index => $row)
                    <tr>
                        <td>{{ $row['product_name'] }}</td>
                        <td>{{ $row['quantity_in_stock'] }}</td>
                        <td>{{ $row['price_per_item'] }}</td>
                        <td>{{ $row['date_submitted'] }}</td>
                        <td>{{ $row['total_value'] }}</td>
                        <td><button class="btn btn-sm btn-warning edit-btn" data-index={{ $index }}>Edit</button>
                        </td>
                    </tr>
                    @php $sumTotal += $row['total_value']; @endphp
                @endforeach
                <tr>
                    <td colspan="4" class="text-end fw-bold">Total:</td>
                    <td colspan="2">{{ $sumTotal }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="actionToast" class="toast align-items-center text-bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    Action successful!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('#productForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/products',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        updateTable(response.data);
                        $('#productForm')[0].reset();

                        showToast('Product added successfully!');
                    }
                });
            });


        });

        $(document).on('click', '.edit-btn', function() {
            const index = $(this).data('index');
            const row = $(this).closest('tr');
            const productName = row.find('td:eq(0)').text();
            const quantity = row.find('td:eq(1)').text();
            const price = row.find('td:eq(2)').text();

            row.html(`
                <td><input type="text" class="form-control" value="${productName}" id="edit-name-${index}"></td>
                <td><input type="number" class="form-control" value="${quantity}" id="edit-quantity-${index}"></td>
                <td><input type="number" step="0.01" class="form-control" value="${price}" id="edit-price-${index}"></td>
                <td></td>
                <td></td>
                <td>
                    <button class="btn btn-sm btn-success save-btn" data-index="${index}">Save</button>
                </td>
            `);
        });

        $(document).on('click', '.save-btn', function() {
            const index = $(this).data('index');
            const productName = $(`#edit-name-${index}`).val();
            const quantity = $(`#edit-quantity-${index}`).val();
            const price = $(`#edit-price-${index}`).val();

            $.ajax({
                url: '/products/update',
                type: 'POST',
                data: {
                    index,
                    product_name: productName,
                    quantity_in_stock: quantity,
                    price_per_item: price,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    updateTable(response.data);

                    showToast('Product updated successfully!');
                },
                error: function() {
                    showToast('Failed to update product. Please try again.');
                }
            });
        });

        function updateTable(data) {
            let rows = '';
            let sumTotal = 0;

            data.forEach((item, index) => {
                rows += `
                    <tr>
                        <td>${item.product_name}</td>
                        <td>${item.quantity_in_stock}</td>
                        <td>${item.price_per_item}</td>
                        <td>${item.date_submitted}</td>
                        <td>${item.total_value}</td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn" data-index="${index}">Edit</button>
                        </td>
                    </tr>
                `;
                sumTotal += item.total_value;
            });

            rows += `
                <tr>
                    <td colspan="4" class="text-end fw-bold">Total:</td>
                    <td colspan="2">${sumTotal}</td>
                </tr>
            `;

            $('#productTable').html(rows);
        }

        function showToast(message) {
            document.getElementById('toastMessage').textContent = message;

            const toastEl = document.getElementById('actionToast');
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    </script>
</body>

</html>
