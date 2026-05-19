// ─── FIX: This entire file was wrong — it contained order-status-update code instead of review logic ───

document.addEventListener('DOMContentLoaded', () => {

    const reviewForm = document.getElementById('review-form');

    if (!reviewForm) return; // not on a page that has a review form

    reviewForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const productId  = this.dataset.productId;
        const ratingInput = document.querySelector('input[name="rating"]:checked');
        const reviewText  = document.getElementById('review-text').value.trim();
        const submitBtn   = document.getElementById('submit-review-btn');
        const msgBox      = document.getElementById('review-message');

        // Clear previous messages
        msgBox.textContent = '';
        msgBox.className   = 'mt-3';

        if (!ratingInput) {
            msgBox.textContent = 'Please select a star rating.';
            msgBox.classList.add('alert', 'alert-warning');
            return;
        }

        if (!reviewText) {
            msgBox.textContent = 'Please write a review before submitting.';
            msgBox.classList.add('alert', 'alert-warning');
            return;
        }

        submitBtn.disabled    = true;
        submitBtn.textContent = 'Submitting…';

        try {

            const response = await fetch('../../api/reviews/submit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    product_id:  productId,
                    rating:      parseInt(ratingInput.value),
                    review_text: reviewText
                })
            });

            const result = await response.json();

            msgBox.classList.add('alert');

            if (result.success) {
                msgBox.classList.add('alert-success');
                msgBox.textContent = result.message;
                reviewForm.reset();
                // Reload reviews list after a short delay
                setTimeout(() => location.reload(), 1500);
            } else {
                msgBox.classList.add('alert-danger');
                msgBox.textContent = result.message;
                submitBtn.disabled    = false;
                submitBtn.textContent = 'Submit Review';
            }

        } catch (error) {
            console.error('Review submit error:', error);
            msgBox.classList.add('alert', 'alert-danger');
            msgBox.textContent    = 'Something went wrong. Please try again.';
            submitBtn.disabled    = false;
            submitBtn.textContent = 'Submit Review';
        }
    });

    // Star rating hover effect
    const starLabels = document.querySelectorAll('.star-label');
    starLabels.forEach(label => {
        label.addEventListener('mouseenter', function () {
            const val = this.dataset.value;
            starLabels.forEach(l => {
                l.classList.toggle('hovered', parseInt(l.dataset.value) <= parseInt(val));
            });
        });
        label.addEventListener('mouseleave', () => {
            starLabels.forEach(l => l.classList.remove('hovered'));
        });
    });
});
