<?php
// ─── FIX: This file was entirely missing — reviews.js calls this endpoint ───

require_once '../../config/database.php';
require_once '../../config/helpers.php';
require_once '../../Model/Review.php';

header('Content-Type: application/json');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a review.']);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data       = json_decode(file_get_contents('php://input'), true);
$productId  = (int)($data['product_id']  ?? 0);
$rating     = (int)($data['rating']      ?? 0);
$reviewText = trim($data['review_text']  ?? '');

// Validate inputs
if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5.']);
    exit;
}

if (empty($reviewText)) {
    echo json_encode(['success' => false, 'message' => 'Review text cannot be empty.']);
    exit;
}

$review = new Review($pdo);

// Prevent duplicate reviews
if ($review->hasReviewed($productId, $_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You have already reviewed this product.']);
    exit;
}

$added = $review->addReview($productId, $_SESSION['user_id'], $rating, $reviewText);

if ($added) {
    echo json_encode(['success' => true, 'message' => 'Review submitted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit review. Please try again.']);
}
?>
