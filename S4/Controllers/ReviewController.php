<?php
require_once '../config/database.php';
require_once '../config/helpers.php';
require_once '../Model/Review.php';

class ReviewController {
    private $review;

    public function __construct($pdo) {
        $this->review = new Review($pdo);
    }

    // ─── FIX: All methods below were missing — the class was entirely empty ───

    /**
     * Show the review form + existing reviews for a product.
     * Called via GET: ?product_id=123
     */
    public function showProductReviews() {
        $productId = (int)($_GET['product_id'] ?? 0);

        if (!$productId) {
            http_response_code(400);
            echo 'Missing product_id';
            return;
        }

        $reviews   = $this->review->getProductReviews($productId);
        $avgRating = $this->review->averageRating($productId);
        $hasReviewed = false;

        if (isset($_SESSION['user_id'])) {
            $hasReviewed = (bool)$this->review->hasReviewed($productId, $_SESSION['user_id']);
        }

        include '../Views/reviews/product_reviews.php';
    }

    /**
     * Handle review submission via regular POST form (non-AJAX fallback).
     * AJAX submissions are handled by api/reviews/submit.php.
     */
    public function submitReview() {
        require_login();

        $productId  = (int)($_POST['product_id'] ?? 0);
        $rating     = (int)($_POST['rating']     ?? 0);
        $reviewText = trim($_POST['review_text'] ?? '');

        if (!$productId || $rating < 1 || $rating > 5) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Prevent duplicate reviews
        if ($this->review->hasReviewed($productId, $_SESSION['user_id'])) {
            $_SESSION['review_error'] = 'You have already reviewed this product.';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $added = $this->review->addReview(
            $productId,
            $_SESSION['user_id'],
            $rating,
            $reviewText
        );

        if ($added) {
            $_SESSION['review_success'] = 'Review submitted successfully!';
        } else {
            $_SESSION['review_error'] = 'Failed to submit review. Please try again.';
        }

        header("Location: ../Views/reviews/product_reviews.php?product_id={$productId}");
        exit;
    }
}
?>
