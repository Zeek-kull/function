<?php
  include 'header.php';
  include 'lib/connection.php';

  // Get all distinct categories for the filter dropdown
  $category_sql = "SELECT DISTINCT category FROM product ORDER BY category";
  $category_result = mysqli_query($conn, $category_sql);
  $categories = [];
  while ($row = mysqli_fetch_assoc($category_result)) {
    $categories[] = $row['category'];
  }

  // Default query - get all products
  $sql = "SELECT * FROM product";
  $result = mysqli_query($conn, $sql);
?>
<div class="container">
    <h5>CLOTHING</h5>
    
    <!-- Filter Dropdown -->
    <div class="filter-section mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="categoryFilter" class="form-label">Filter by Category:</label>
                <select id="categoryFilter" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>">
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Products Container -->
    <div class="container">
        <div class="row" id="productsContainer">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="col-md-3 col-sm-6 col-6 product-item" data-category="<?php echo htmlspecialchars($row['category']); ?>">
                        <div class="product-item">
                            <a href="product_detail.php?id=<?php echo $row['p_id']; ?>" class="product-link">
                                <div class="product-image">
                                    <img src="admin/product_img/<?php echo $row['imgname']; ?>" alt="<?php echo $row['name']; ?>">
                                </div>
                                <div class="product-info">
                                    <h6><?php echo $row["name"]; ?></h6>
                                    <span class="price"><?php echo number_format($row["Price"], 2); ?></span>
                                    <small class="text-muted d-block"><?php echo $row["category"]; ?></small>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='col-12'><p>No products available.</p></div>";
            }
            ?>
        </div>
    </div>
</div>

<!-- AJAX Script -->
<script src="js/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#categoryFilter').change(function() {
        var selectedCategory = $(this).val();
        
        $.ajax({
            url: 'filter_products.php',
            type: 'POST',
            data: { category: selectedCategory },
            success: function(response) {
                $('#productsContainer').html(response);
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                alert('Error loading products. Please try again.');
            }
        });
    });
});
</script>

<?php
  include 'footer.php';
?>
