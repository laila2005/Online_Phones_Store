// Main JavaScript file for Online Phones Store

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Add to Cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');

            const originalText = this.innerHTML;
            const originalDisabled = this.disabled;

            this.disabled = true;
            this.innerHTML = 'Adding...';

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', '1');

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                // Check if redirect is needed (user not logged in)
                if (data && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                if (!data || !data.success) {
                    throw new Error((data && data.message) ? data.message : 'Failed to add to cart');
                }

                this.innerHTML = 'Added!';
                this.classList.remove('btn-success');
                this.classList.add('btn-secondary');

                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-secondary');
                    this.classList.add('btn-success');
                    this.disabled = originalDisabled;
                }, 1200);
            })
            .catch(err => {
                alert(err && err.message ? err.message : 'Could not add to cart');
                this.innerHTML = originalText;
                this.disabled = originalDisabled;
            });
        });
    });
    
    // Wishlist functionality
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            const heartIcon = this.querySelector('i');

            const formData = new FormData();
            formData.append('product_id', productId);

            fetch('add_to_wishlist.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                // Check if redirect is needed (user not logged in)
                if (data && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                if (!data || !data.success) {
                    throw new Error((data && data.message) ? data.message : 'Failed to update wishlist');
                }

                // Toggle heart icon
                if (data.action === 'added') {
                    heartIcon.classList.remove('bi-heart');
                    heartIcon.classList.add('bi-heart-fill');
                    
                    // Show success message
                    const toast = document.createElement('div');
                    toast.className = 'position-fixed bottom-0 end-0 p-3';
                    toast.style.zIndex = '11';
                    toast.innerHTML = `
                        <div class="toast show" role="alert">
                            <div class="toast-body bg-success text-white rounded">
                                <i class="bi bi-heart-fill me-2"></i>Added to wishlist!
                            </div>
                        </div>
                    `;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 2000);
                } else {
                    heartIcon.classList.remove('bi-heart-fill');
                    heartIcon.classList.add('bi-heart');
                }
            })
            .catch(err => {
                alert(err && err.message ? err.message : 'Could not update wishlist');
            });
        });
    });
    
});
