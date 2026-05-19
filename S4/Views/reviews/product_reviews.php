<?php

require_once '../../config/database.php';
require_once '../../config/helpers.php';

/*
|--------------------------------------------------------------------------
| START SESSION
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| DEMO USER
|--------------------------------------------------------------------------
*/

$_SESSION['user_id'] = 1;

$userId = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| PRODUCT ID
|--------------------------------------------------------------------------
*/

$productId = (int)($_GET['product_id'] ?? 1);

/*
|--------------------------------------------------------------------------
| SUBMIT REVIEW
|--------------------------------------------------------------------------
*/

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $rating = (int)($_POST['rating'] ?? 0);

    $reviewText = trim($_POST['review_text'] ?? '');

    if ($rating < 1 || $rating > 5) {

        $error = 'Please select rating';

    } elseif (empty($reviewText)) {

        $error = 'Review text is required';

    } else {

        /*
        |--------------------------------------------------------------------------
        | CHECK ALREADY REVIEWED
        |--------------------------------------------------------------------------
        */

        $checkSql = "
        SELECT id
        FROM reviews
        WHERE product_id = ?
        AND user_id = ?
        ";

        $stmt = $pdo->prepare($checkSql);

        $stmt->execute([$productId, $userId]);

        $exists = $stmt->fetch();

        if ($exists) {

            $error = 'You already reviewed this product';

        } else {

            /*
            |--------------------------------------------------------------------------
            | INSERT REVIEW
            |--------------------------------------------------------------------------
            */

            $insertSql = "
            INSERT INTO reviews
            (
                product_id,
                user_id,
                rating,
                review_text
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
            ";

            $stmt = $pdo->prepare($insertSql);

            $stmt->execute([
                $productId,
                $userId,
                $rating,
                $reviewText
            ]);

            $success = 'Review submitted successfully';

        }

    }

}

/*
|--------------------------------------------------------------------------
| FETCH REVIEWS
|--------------------------------------------------------------------------
*/

$sql = "
SELECT 
    reviews.*,
    users.name
FROM reviews
LEFT JOIN users
    ON reviews.user_id = users.id
WHERE reviews.product_id = ?
ORDER BY reviews.id DESC
";

$stmt = $pdo->prepare($sql);

$stmt->execute([$productId]);

$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| AVG RATING
|--------------------------------------------------------------------------
*/

$avgSql = "
SELECT 
    AVG(rating) AS avg_rating
FROM reviews
WHERE product_id = ?
";

$stmt = $pdo->prepare($avgSql);

$stmt->execute([$productId]);

$avgRating = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| HAS REVIEWED
|--------------------------------------------------------------------------
*/

$checkSql = "
SELECT id
FROM reviews
WHERE product_id = ?
AND user_id = ?
";

$stmt = $pdo->prepare($checkSql);

$stmt->execute([$productId, $userId]);

$hasReviewed = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Product Reviews</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>

        body{
            background:#f5f6fa;
        }

        .star-rating{
            display:flex;
            flex-direction:row-reverse;
            gap:4px;
        }

        .star-rating input{
            display:none;
        }

        .star-label{
            font-size:32px;
            cursor:pointer;
            color:#ccc;
        }

        .star-rating input:checked ~ .star-label{
            color:#f5a623;
        }

        .star-display{
            color:#f5a623;
            font-size:18px;
        }

        .star-display.empty{
            color:#ccc;
        }

    </style>

</head>

<body>

<div class="container py-5">

    <?php

    $avgVal = round((float)($avgRating['avg_rating'] ?? 0), 1);

    $reviewCount = count($reviews);

    ?>

    <!-- AVG -->
    <div class="d-flex align-items-center gap-3 mb-4">

        <div class="display-6 fw-bold">

            <?= $avgVal > 0 ? $avgVal : '—' ?>

        </div>

        <div>

            <div>

                <?php for ($i = 1; $i <= 5; $i++): ?>

                    <span class="star-display <?= $i <= round($avgVal) ? '' : 'empty' ?>">

                        ★

                    </span>

                <?php endfor; ?>

            </div>

            <small class="text-muted">

                <?= $reviewCount ?>

                review<?= $reviewCount != 1 ? 's' : '' ?>

            </small>

        </div>

    </div>

    <!-- SUCCESS -->
    <?php if (!empty($success)): ?>

        <div class="alert alert-success">

            <?= e($success) ?>

        </div>

    <?php endif; ?>

    <!-- ERROR -->
    <?php if (!empty($error)): ?>

        <div class="alert alert-danger">

            <?= e($error) ?>

        </div>

    <?php endif; ?>

    <!-- REVIEW FORM -->
    <?php if (!$hasReviewed): ?>

        <div class="card mb-5 shadow-sm">

            <div class="card-header bg-dark text-white fw-bold">

                Write a Review

            </div>

            <div class="card-body">

                <form method="POST">

                    <!-- STAR -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Your Rating

                        </label>

                        <div class="star-rating">

                            <?php for ($i = 5; $i >= 1; $i--): ?>

                                <input type="radio"
                                       id="star<?= $i ?>"
                                       name="rating"
                                       value="<?= $i ?>">

                                <label class="star-label"
                                       for="star<?= $i ?>">

                                    ★

                                </label>

                            <?php endfor; ?>

                        </div>

                    </div>

                    <!-- REVIEW -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Your Review

                        </label>

                        <textarea
                            name="review_text"
                            class="form-control"
                            rows="4"
                            required></textarea>

                    </div>

                    <button type="submit"
                            class="btn btn-primary px-4">

                        Submit Review

                    </button>

                </form>

            </div>

        </div>

    <?php else: ?>

        <div class="alert alert-info">

            You already reviewed this product

        </div>

    <?php endif; ?>

    <!-- REVIEWS -->
    <h4 class="mb-3">

        Customer Reviews

    </h4>

    <?php if (!empty($reviews)): ?>

        <?php foreach ($reviews as $review): ?>

            <div class="card mb-3 shadow-sm">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <strong>

                                <?= e($review['name']) ?>

                            </strong>

                            <div class="my-1">

                                <?php for ($i = 1; $i <= 5; $i++): ?>

                                    <span class="star-display <?= $i <= $review['rating'] ? '' : 'empty' ?>">

                                        ★

                                    </span>

                                <?php endfor; ?>

                            </div>

                            <p class="mb-0 mt-2">

                                <?= nl2br(e($review['review_text'])) ?>

                            </p>

                        </div>

                        <small class="text-muted">

                            <?php

                            if (!empty($review['created_at'])) {

                                echo date(
                                    'd M Y',
                                    strtotime($review['created_at'])
                                );

                            } else {

                                echo 'N/A';

                            }

                            ?>

                        </small>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="alert alert-light border">

            No reviews yet

        </div>

    <?php endif; ?>

</div>

</body>

</html>