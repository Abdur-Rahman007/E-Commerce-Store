<?php require_once '../../config/helpers.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
    <style>
        /* Star rating widget */
        .star-rating { display: flex; flex-direction: row-reverse; gap: 4px; }
        .star-rating input[type="radio"] { display: none; }
        .star-label {
            font-size: 1.8rem; cursor: pointer; color: #ccc;
            transition: color .15s;
        }
        .star-rating input:checked ~ .star-label,
        .star-label.hovered { color: #f5a623; }
        /* Read-only stars in reviews list */
        .star-display { color: #f5a623; font-size: 1rem; }
        .star-display.empty { color: #ccc; }
    </style>
</head>
<body>

<div class="container py-5">

    <?php
    $productId   = (int)($_GET['product_id'] ?? 0);
    $avgVal      = round((float)($avgRating['avg_rating'] ?? 0), 1);
    $reviewCount = count($reviews);
    ?>

    <!-- Average rating summary -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="display-6 fw-bold"><?= $avgVal > 0 ? $avgVal : '—' ?></div>
        <div>
            <div>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="star-display <?= $i <= round($avgVal) ? '' : 'empty' ?>">★</span>
                <?php endfor; ?>
            </div>
            <small class="text-muted"><?= $reviewCount ?> review<?= $reviewCount !== 1 ? 's' : '' ?></small>
        </div>
    </div>

    <!-- ─── FIX: Review submission form — was completely missing ─── -->
    <?php if (isset($_SESSION['user_id'])): ?>

        <?php if ($hasReviewed): ?>
            <div class="alert alert-info mb-4">You have already reviewed this product.</div>
        <?php else: ?>
            <div class="card mb-5 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">Write a Review</div>
                <div class="card-body">

                    <form id="review-form" data-product-id="<?= $productId ?>">

                        <!-- Star picker -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Your Rating <span class="text-danger">*</span></label>
                            <div class="star-rating" id="star-picker">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>">
                                    <label class="star-label" for="star<?= $i ?>" data-value="<?= $i ?>">★</label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Review text -->
                        <div class="mb-3">
                            <label for="review-text" class="form-label fw-semibold">
                                Your Review <span class="text-danger">*</span>
                            </label>
                            <textarea id="review-text" class="form-control" rows="4"
                                      placeholder="Share your experience with this product…"
                                      maxlength="1000"></textarea>
                            <div class="form-text">Max 1000 characters.</div>
                        </div>

                        <!-- Feedback message area -->
                        <div id="review-message"></div>

                        <button id="submit-review-btn" type="submit" class="btn btn-primary px-4">
                            Submit Review
                        </button>

                    </form>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-warning mb-4">
            <a href="/login.php">Log in</a> to submit a review.
        </div>
    <?php endif; ?>

    <!-- Reviews list -->
    <h4 class="mb-3">Customer Reviews</h4>

    <?php if (!empty($reviews)): ?>

        <?php foreach ($reviews as $review): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong><?= e($review['name']) ?></strong>
                            <div class="my-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star-display <?= $i <= $review['rating'] ? '' : 'empty' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <p class="mb-0 mt-2"><?= nl2br(e($review['review_text'])) ?></p>
                        </div>
                        <small class="text-muted ms-3 text-nowrap">
                            <?= e(date('d M Y', strtotime($review['created_at']))) ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="alert alert-light border">No reviews yet. Be the first!</div>
    <?php endif; ?>

</div>

<script src="../../public/js/reviews.js"></script>

</body>
</html>
