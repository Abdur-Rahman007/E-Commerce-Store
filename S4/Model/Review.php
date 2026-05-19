<?php
class Review {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addReview($productId, $userId, $rating, $reviewText) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO reviews(product_id, user_id, rating, review_text, created_at)
             VALUES(?,?,?,?,NOW())"
        );

        return $stmt->execute([$productId, $userId, $rating, $reviewText]);
    }

    public function hasReviewed($productId, $userId) {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM reviews WHERE product_id = ? AND user_id = ?"
        );

        $stmt->execute([$productId, $userId]);

        return $stmt->fetch();
    }

    public function getProductReviews($productId) {
        $stmt = $this->pdo->prepare(
            "SELECT reviews.*, users.name
             FROM reviews
             JOIN users ON reviews.user_id = users.id
             WHERE product_id = ?
             ORDER BY created_at DESC"
        );

        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function averageRating($productId) {
        $stmt = $this->pdo->prepare(
            "SELECT AVG(rating) as avg_rating
             FROM reviews
             WHERE product_id = ?"
        );

        $stmt->execute([$productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>