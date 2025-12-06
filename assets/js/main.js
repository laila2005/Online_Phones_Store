// Main JavaScript file for Online Phones Store

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Add to Cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            
            // For now, just show an alert
            // This will be replaced with actual cart functionality later
            alert('Product ' + productId + ' added to cart!\n\nCart functionality will be implemented in the next phase.');
            
            // Optional: Add visual feedback
            const originalText = this.innerHTML;
            this.innerHTML = 'Added!';
            this.classList.remove('btn-success');
            this.classList.add('btn-secondary');
            this.disabled = true;
            
            // Reset button after 2 seconds
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.remove('btn-secondary');
                this.classList.add('btn-success');
                this.disabled = false;
            }, 2000);
        });
    });
    
});
