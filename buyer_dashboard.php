
<?php
session_start();
include_once('db.php');

if(!isset($_SESSION['User_ID']) || $_SESSION['Role']!='buyer'){
    header("Location: login_page.php");
    exit;
}

/* ================= SEARCH ================= */
$search = '';
if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn,$_GET['search']);
    $products = mysqli_query($conn,
        "SELECT * FROM products 
         WHERE product_name LIKE '%$search%' 
         OR description LIKE '%$search%' 
         ORDER BY product_id DESC"
    );
}else{
    $products = mysqli_query($conn,"SELECT * FROM products ORDER BY product_id DESC");
}

/* ================= CART COUNT ================= */
$cartCount = 0;
if(isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $qty){
        $cartCount += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buyer Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --secondary: #6b7280;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --light: #f9fafb;
        --dark: #111827;
        --gray: #9ca3af;
        --border: #e5e7eb;
        --card-shadow: 0 1px 3px rgba(0,0,0,0.1);
        --hover-shadow: 0 10px 25px rgba(0,0,0,0.1);
        --radius: 8px;
        --transition: all 0.2s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        background-color: var(--light);
        color: var(--dark);
        min-height: 100vh;
    }

    /* Header */
    .header {
        background: white;
        padding: 1rem 2rem;
        border-bottom: 1px solid var(--border);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .logo-icon {
        width: 36px;
        height: 36px;
        background: var(--primary);
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    .logo-text {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
    }

    .user-actions {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .welcome-text {
        font-size: 0.95rem;
        color: var(--secondary);
    }

    .welcome-text strong {
        color: var(--primary);
    }

    .nav-buttons {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .nav-btn {
        padding: 0.6rem 1.2rem;
        border-radius: var(--radius);
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
    }

    .nav-btn.outline {
        border: 1px solid var(--border);
        color: var(--dark);
        background: white;
    }

    .nav-btn.outline:hover {
        background: var(--light);
        border-color: var(--primary);
        color: var(--primary);
    }

    .nav-btn.primary {
        background: var(--primary);
        color: white;
        border: 1px solid var(--primary);
    }

    .nav-btn.primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: var(--hover-shadow);
    }

    .badge {
        background: var(--danger);
        color: white;
        font-size: 0.75rem;
        padding: 0.15rem 0.4rem;
        border-radius: 20px;
        margin-left: 4px;
    }

    /* Main Content */
    .container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 2rem;
    }

    /* Dashboard Header */
    .dashboard-header {
        margin-bottom: 2.5rem;
    }

    .dashboard-title {
        margin-bottom: 1rem;
    }

    .dashboard-title h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .dashboard-title p {
        color: var(--secondary);
        font-size: 1rem;
    }

    /* Search */
    .search-container {
        max-width: 500px;
        position: relative;
    }

    .search-box {
        width: 100%;
        padding: 0.8rem 1rem 0.8rem 3rem;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        font-size: 1rem;
        background: white;
        transition: var(--transition);
    }

    .search-box:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray);
    }

    /* Products Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    /* Product Card */
    .product-card {
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        border: 1px solid var(--border);
        transition: var(--transition);
        box-shadow: var(--card-shadow);
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--hover-shadow);
        border-color: var(--primary);
    }

    .product-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .stock-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: var(--success);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .stock-badge.out {
        background: var(--danger);
    }

    .product-content {
        padding: 1.25rem;
    }

    .product-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .product-description {
        color: var(--secondary);
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 1rem;
    }

    .product-price .currency {
        font-size: 1rem;
        color: var(--gray);
    }

    .product-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .btn {
        flex: 1;
        padding: 0.7rem;
        border: none;
        border-radius: var(--radius);
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn.primary {
        background: var(--primary);
        color: white;
    }

    .btn.primary:hover {
        background: var(--primary-hover);
    }

    .btn.outline {
        background: white;
        color: var(--dark);
        border: 1px solid var(--border);
    }

    .btn.outline:hover {
        background: var(--light);
        border-color: var(--primary);
        color: var(--primary);
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Modal */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 1rem;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
    }

    .modal.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: var(--radius);
        width: 100%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px rgba(0,0,0,0.1);
        transform: scale(0.95);
        transition: transform 0.3s ease;
    }

    .modal.active .modal-content {
        transform: scale(1);
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--dark);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--gray);
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 4px;
        transition: var(--transition);
    }

    .modal-close:hover {
        background: var(--light);
        color: var(--dark);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    @media (max-width: 768px) {
        .modal-grid {
            grid-template-columns: 1fr;
        }
    }

    .modal-image-container {
        position: relative;
    }

    .modal-main-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: var(--radius);
        margin-bottom: 1rem;
    }

    .modal-thumbnails {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding: 0.5rem 0;
    }

    .modal-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: var(--transition);
    }

    .modal-thumbnail:hover,
    .modal-thumbnail.active {
        border-color: var(--primary);
    }

    .modal-details h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 1rem;
    }

    .modal-info {
        background: var(--light);
        padding: 1rem;
        border-radius: var(--radius);
        margin: 1.5rem 0;
    }

    .modal-info-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border);
    }

    .modal-info-item:last-child {
        border-bottom: none;
    }

    .modal-label {
        color: var(--secondary);
        font-weight: 500;
    }

    .modal-value {
        color: var(--dark);
        font-weight: 600;
    }

    .modal-description {
        color: var(--secondary);
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .modal-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .modal-actions .btn {
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--gray);
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: var(--dark);
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
    }

    .empty-state p {
        color: var(--secondary);
    }

    /* Notification */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--success);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: var(--radius);
        box-shadow: var(--hover-shadow);
        z-index: 1001;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.3s ease;
    }

    .notification.error {
        background: var(--danger);
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-container {
            flex-direction: column;
            gap: 1rem;
        }
        
        .user-actions {
            width: 100%;
            justify-content: space-between;
        }
        
        .container {
            padding: 0 1rem;
        }
        
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
        
        .nav-buttons {
            gap: 0.5rem;
        }
        
        .nav-btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .products-grid {
            grid-template-columns: 1fr;
        }
        
        .modal-actions {
            flex-direction: column;
        }
        
        .product-actions {
            flex-direction: column;
        }
        
        .nav-buttons {
            flex-wrap: wrap;
        }
    }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="#" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="logo-text">ShopEasy</div>
            </a>
            
            <div class="user-actions">
                <div class="welcome-text">
                    Welcome, <strong><?php echo $_SESSION['Username']; ?></strong>
                </div>
                
                <div class="nav-buttons">
                    <a href="cart.php" class="nav-btn primary">
                        <i class="fas fa-shopping-cart"></i>
                        Cart
                        <?php if($cartCount > 0): ?>
                            <span class="badge"><?php echo $cartCount; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="my_orders.php" class="nav-btn outline">
                        <i class="fas fa-box"></i>
                        Orders
                    </a>
                    <a href="logout.php" class="nav-btn outline">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="dashboard-header">
            <div class="dashboard-title">
                <h1>Product Marketplace</h1>
                <p>Browse and purchase products from our sellers</p>
            </div>
            
            <form method="GET" class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" 
                       name="search" 
                       class="search-box" 
                       placeholder="Search products..." 
                       value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>

        <!-- Products Grid -->
        <div class="products-grid">
            <?php 
            if(mysqli_num_rows($products) > 0):
                while($p = mysqli_fetch_assoc($products)):
                    $img = 'no-image.png';
                    $allImages = ['no-image.png'];
                    if(!empty($p['images'])) {
                        $allImages = explode(',', $p['images']);
                        $img = $allImages[0];
                    }
                    $stock = isset($p['quantity']) ? intval($p['quantity']) : 0;
                    $inStock = $stock > 0;
            ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="uploads/<?php echo $img; ?>" 
                         alt="<?php echo htmlspecialchars($p['product_name']); ?>"
                         class="open-modal"
                         data-id="<?php echo $p['product_id']; ?>"
                         data-name="<?php echo htmlspecialchars($p['product_name']); ?>"
                         data-price="<?php echo number_format($p['price']); ?>"
                         data-desc="<?php echo htmlspecialchars($p['description']); ?>"
                         data-img="uploads/<?php echo $img; ?>"
                         data-all-images="<?php echo htmlspecialchars(implode(',', $allImages)); ?>"
                         data-qty="<?php echo $stock; ?>"
                         data-seller="<?php echo isset($p['seller_id']) ? $p['seller_id'] : 'N/A'; ?>"
                         data-category="<?php echo isset($p['category']) ? $p['category'] : 'General'; ?>">
                    
                    <div class="stock-badge <?php echo $inStock ? '' : 'out'; ?>" data-qty="<?php echo $stock; ?>">
                        <?php echo $inStock ? 'In Stock' : 'Out of Stock'; ?>
                    </div>
                </div>
                
                <div class="product-content">
                    <h3 class="product-title"><?php echo $p['product_name']; ?></h3>
                    <p class="product-description"><?php echo htmlspecialchars(substr($p['description'], 0, 100)); ?>...</p>
                    
                    <div class="product-price">
                        <span class="currency">Rs</span>
                        <span><?php echo number_format($p['price']); ?></span>
                    </div>
                    
                    <div class="product-actions">
                        <a href="place_order.php?product_id=<?php echo $p['product_id']; ?>" 
                           class="btn primary <?php echo !$inStock ? 'disabled' : ''; ?>"
                           <?php echo !$inStock ? 'disabled' : ''; ?>>
                            <i class="fas fa-bolt"></i> Buy Now
                        </a>
                        <button class="btn outline add-to-cart-btn" 
                                data-id="<?php echo $p['product_id']; ?>"
                                <?php echo !$inStock ? 'disabled' : ''; ?>>
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>No products found</h3>
                <p><?php echo $search ? 'Try a different search term' : 'Check back later for new products'; ?></p>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Product Modal -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Product Details</h2>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-grid">
                    <div class="modal-image-container">
                        <img id="modalMainImage" src="" alt="Product Image" class="modal-main-image">
                        <div class="modal-thumbnails" id="thumbnailGallery"></div>
                    </div>
                    
                    <div class="modal-details">
                        <h3 id="modalProductName"></h3>
                        <div class="modal-price">
                            <span class="currency">Rs</span>
                            <span id="modalProductPrice"></span>
                        </div>
                        
                        <div class="modal-info">
                            <div class="modal-info-item">
                                <span class="modal-label">Product ID:</span>
                                <span class="modal-value" id="modalProductId"></span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-label">Seller:</span>
                                <span class="modal-value" id="modalSellerId"></span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-label">Category:</span>
                                <span class="modal-value" id="modalCategory"></span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-label">Stock:</span>
                                <span class="modal-value" id="modalStock"></span>
                            </div>
                        </div>
                        
                        <p class="modal-description" id="modalProductDescription"></p>
                        
                        <div class="modal-actions">
                            <a href="#" id="modalBuyBtn" class="btn primary">
                                <i class="fas fa-bolt"></i> Buy Now
                            </a>
                            <button id="modalCartBtn" class="btn outline">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let cartCount = <?php echo $cartCount; ?>;

    function addToCart(productId, button) {
        const originalHtml = $(button).html();
        $(button).html('<i class="fas fa-spinner fa-spin"></i>');
        $(button).prop('disabled', true);

        $.get('cart.php?add=' + productId, function() {
            cartCount++;
            $('.badge').text(cartCount);

            // Update stock in UI
            let card = $(button).closest('.product-card');
            let stockBadge = card.find('.stock-badge');
            let qty = parseInt(stockBadge.data('qty') || 0);
            qty--; // decrement stock

            if(qty <= 0){
                qty = 0;
                stockBadge.text('Out of Stock').addClass('out');
                // disable Buy Now and Add to Cart buttons
                card.find('.btn').prop('disabled', true).addClass('disabled');
            } else {
                stockBadge.text('In Stock').removeClass('out');
            }
            stockBadge.data('qty', qty);

            // Update modal buttons if modal is open
            if($('#productModal').hasClass('active')){
                $('#modalStock').text(qty > 0 ? qty + ' units available' : 'Out of Stock');
                if(qty <= 0){
                    $('#modalBuyBtn, #modalCartBtn').prop('disabled', true).addClass('disabled');
                }
            }

            $(button).html('<i class="fas fa-check"></i> Added');
            showNotification('Product added to cart!', 'success');

            setTimeout(() => {
                $(button).html(originalHtml);
                if(qty > 0) $(button).prop('disabled', false);
            }, 1500);
        }).fail(function() {
            $(button).html(originalHtml);
            $(button).prop('disabled', false);
            showNotification('Failed to add product', 'error');
        });
    }

    function showNotification(message, type) {
        const notification = $(`
            <div class="notification ${type === 'error' ? 'error' : ''}">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                ${message}
            </div>
        `);
        $('body').append(notification);
        setTimeout(() => {
            notification.fadeOut(300, () => notification.remove());
        }, 3000);
    }

    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        const productId = $(this).data('id');
        addToCart(productId, this);
    });

    $(document).on('click', '.open-modal', function() {
        const data = $(this).data();
        $('#modalMainImage').attr('src', data.img);
        $('#modalProductName').text(data.name);
        $('#modalProductPrice').text(data.price);
        $('#modalProductDescription').text(data.desc);
        $('#modalProductId').text(data.id);
        $('#modalSellerId').text('Seller #' + (data.seller || 'N/A'));
        $('#modalCategory').text(data.category || 'General');
        $('#modalStock').text(data.qty > 0 ? data.qty + ' units available' : 'Out of Stock');

        $('#modalBuyBtn').attr('href', 'place_order.php?product_id=' + data.id);
        $('#modalBuyBtn, #modalCartBtn').prop('disabled', data.qty <= 0).toggleClass('disabled', data.qty <= 0);

        $('#modalCartBtn').off('click').on('click', function() {
            addToCart(data.id, this);
        });

        let thumbnailsHtml = '';
        const allImages = data.allImages ? data.allImages.split(',') : [data.img];
        allImages.forEach((image, index) => {
            let imagePath = image.trim() || 'no-image.png';
            const isActive = index === 0 ? 'active' : '';
            thumbnailsHtml += `<img src="uploads/${imagePath}" class="modal-thumbnail ${isActive}" onclick="$('#modalMainImage').attr('src', 'uploads/${imagePath}'); $(this).siblings().removeClass('active'); $(this).addClass('active');" alt="Thumbnail ${index + 1}">`;
        });
        $('#thumbnailGallery').html(thumbnailsHtml);

        $('#productModal').addClass('active');
        $('body').css('overflow', 'hidden');
    });

    $('#closeModal, #productModal').click(function(e) {
        if (e.target === this || $(e.target).hasClass('modal-close')) {
            $('#productModal').removeClass('active');
            $('body').css('overflow', 'auto');
        }
    });

    $(document).keydown(function(e) {
        if (e.key === 'Escape' && $('#productModal').hasClass('active')) {
            $('#productModal').removeClass('active');
            $('body').css('overflow', 'auto');
        }
    });

    $('.search-box').on('keypress', function(e) {
        if (e.which === 13) $(this).closest('form').submit();
    });
});
</script>
</body>
</html>
