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
    
});
