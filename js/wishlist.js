/**
 * Wishlist AJAX Functionality
 * Handles adding/removing posts from wishlist without page reload
 */

document.addEventListener('DOMContentLoaded', function() {
    const wishlistButtons = document.querySelectorAll('.btn-wishlist');

    wishlistButtons.forEach(button => {
        // Check if post is already in wishlist
        checkWishlistStatus(button);

        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const isInWishlist = this.classList.contains('in-wishlist');

            if (isInWishlist) {
                removeFromWishlist(this, postId);
            } else {
                addToWishlist(this, postId);
            }
        });
    });

    async function checkWishlistStatus(button) {
        const postId = button.dataset.postId;
        const apiRemove = button.dataset.apiRemove || '/api/wishlist.php?action=remove';
        const apiCheck = apiRemove.replace('action=remove', 'action=check');

        try {
            const response = await fetch(`${apiCheck}&post_id=${postId}`);
            const data = await response.json();

            if (data.in_wishlist) {
                button.classList.add('in-wishlist');
                button.textContent = '❤️ Saved';
            }
        } catch (error) {
            console.error('Error checking wishlist status:', error);
        }
    }

    async function addToWishlist(button, postId) {
        const originalText = button.textContent;
        const apiAdd = button.dataset.apiAdd || '/api/wishlist.php?action=add';

        button.disabled = true;
        button.textContent = 'Adding...';

        try {
            const formData = new FormData();
            formData.append('post_id', postId);

            const response = await fetch(apiAdd, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                button.classList.add('in-wishlist');
                button.textContent = '❤️ Saved';

                // Update wishlist count in navbar if it exists
                updateWishlistCount(data.count);
            } else {
                button.textContent = originalText;
                alert(data.error || 'Failed to add to wishlist');
            }
        } catch (error) {
            button.textContent = originalText;
            alert('An error occurred. Please try again.');
        } finally {
            button.disabled = false;
        }
    }

    async function removeFromWishlist(button, postId) {
        const originalText = button.textContent;
        const apiRemove = button.dataset.apiRemove || '/api/wishlist.php?action=remove';

        button.disabled = true;
        button.textContent = 'Removing...';

        try {
            const formData = new FormData();
            formData.append('post_id', postId);

            const response = await fetch(apiRemove, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                button.classList.remove('in-wishlist');
                button.textContent = '❤️ Add to Wishlist';

                // Update wishlist count in navbar if it exists
                updateWishlistCount(data.count);
            } else {
                button.textContent = originalText;
                alert(data.error || 'Failed to remove from wishlist');
            }
        } catch (error) {
            button.textContent = originalText;
            alert('An error occurred. Please try again.');
        } finally {
            button.disabled = false;
        }
    }

    function updateWishlistCount(count) {
        // This can be used to update a wishlist counter in the navbar
        const wishlistLink = document.querySelector('a[href*="wishlist"]');
        if (wishlistLink && count !== undefined) {
            // Update or create count badge
            let badge = wishlistLink.querySelector('.wishlist-count');
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'wishlist-count';
                wishlistLink.appendChild(badge);
            }
            badge.textContent = count > 0 ? ` (${count})` : '';
        }
    }
});
