// ─── FIX: orders.js now wrapped in DOMContentLoaded, uses relative path (not hardcoded localhost) ───

document.addEventListener('DOMContentLoaded', () => {

    const dropdowns = document.querySelectorAll('.status-dropdown');

    dropdowns.forEach((dropdown) => {

        dropdown.addEventListener('change', async function () {

            const orderId = this.dataset.id;
            const status  = this.value;
            const originalValue = this.dataset.current; // track the previous value

            try {

                const response = await fetch('../../api/orders/update.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId, status: status })
                });

                const result = await response.json();

                if (result.success) {

                    alert(result.message);

                    // Update badge colour instantly without a page reload
                    const row   = this.closest('tr');
                    const badge = row.querySelector('.badge');

                    badge.textContent = status;

                    badge.classList.remove(
                        'bg-warning', 'bg-primary', 'bg-info',
                        'bg-success', 'bg-danger', 'bg-secondary'
                    );

                    const colourMap = {
                        'Pending':    'bg-warning',
                        'Processing': 'bg-primary',
                        'Shipped':    'bg-info',
                        'Delivered':  'bg-success',
                        'Cancelled':  'bg-danger'
                    };
                    badge.classList.add(colourMap[status] ?? 'bg-secondary');

                    // Update stored current value so next change still works
                    this.dataset.current = status;

                } else {
                    alert(result.message);
                    // Revert dropdown to the previous value
                    this.value = this.dataset.current;
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
                this.value = this.dataset.current;
            }
        });
    });
});
