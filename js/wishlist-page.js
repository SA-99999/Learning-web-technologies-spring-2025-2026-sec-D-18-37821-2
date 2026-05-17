/**
 * Wishlist Page Functionality
 * Handles removing items from wishlist page
 */

document.addEventListener('DOMContentLoaded', function() {
    const removeButtons = document.querySelectorAll('.btn-remove');

    removeButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const postId = this.dataset.postId;
            const title = this.dataset.title;
            const apiRemove = this.dataset.apiRemove || '/api/wishlist.php?action=remove';
            const wishlistItem = this.closest('.wishlist-item');

            if (!confirm(`Remove "${title}" from your wishlist?`)) {
                return;
            }

            const originalText = this.textContent;
            this.disabled = true;
            this.textContent = 'Removing...';

            try {
                const formData = new FormData();
                formData.append('post_id', postId);

                const response = await fetch(apiRemove, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Animate removal
                    wishlistItem.style.opacity = '0';
                    wishlistItem.style.transform = 'translateX(-20px)';

                    setTimeout(() => {
                        wishlistItem.remove();

                        // Check if wishlist is now empty
                        const remainingItems = document.querySelectorAll('.wishlist-item');
                        if (remainingItems.length === 0) {
                            location.reload();
                        }
                    }, 300);

                    showAlert('success', data.message);
                } else {
                    this.textContent = originalText;
                    this.disabled = false;
                    showAlert('danger', data.error || 'Failed to remove from wishlist');
                }
            } catch (error) {
                this.textContent = originalText;
                this.disabled = false;
                showAlert('danger', 'An error occurred. Please try again.');
            }
        });
    });

    function showAlert(type, message) {
        const alertsContainer = document.getElementById('wishlist-alerts');
        alertsContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            alertsContainer.innerHTML = '';
        }, 3000);
    }
});
