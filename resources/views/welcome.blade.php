<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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


    </div>

    <script>
        $(document).ready(function() {
            $('#productForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/products',
                    type: 'POST',
                });
            });


        });
    </script>
</body>

</html>
