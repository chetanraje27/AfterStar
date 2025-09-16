<?php
header('Content-Type: text/html; charset=UTF-8');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Calculate performance for each movie
$query = "
    SELECT 
        m.movie_id,
        m.movie_title,
        m.posters,
        m.release_date,
        m.genera AS genre,
        COUNT(r.rating_id) AS rating_count,
        AVG(r.rating) AS avg_rating,
        DATEDIFF(CURRENT_DATE, MIN(r.created_at)) AS days_since_first_rating,
        ROUND(
            (
                ((1 - (LEAST(DATEDIFF(CURRENT_DATE, MIN(r.created_at)), 100) / 100)) * 100) +  -- n (0-100)
                LEAST(COUNT(r.rating_id), 100) +  -- r (0-100)
                (AVG(r.rating) * 20)  -- AVG_r (0-100)
            ) / 3, 2
        ) AS performance_score
    FROM movie m
    LEFT JOIN ratings r ON m.movie_id = r.movie_id
    GROUP BY m.movie_id, m.movie_title, m.posters, m.release_date, m.genera
    HAVING rating_count > 0
    ORDER BY performance_score DESC
    LIMIT 10
";

$result = $conn->query($query);
$movies = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
} else {
    die("Query failed: " . $conn->error); // This will show the exact SQL error
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Performers - AfterStar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header (same as your main site) -->
    <header>
        <div class="container">
            <nav>
                <a href="afterstar-website.html" class="logo">After<span>Star</span></a>
                <div class="nav-links">
                    <a href="afterstar-website.html">Home</a>
                    <a href="#movies">Movies</a>
                    <a href="top_performers.php">Top Performers</a>
                    <a href="#rate">Rate a Movie</a>
                    <a href="#about">About</a>
                </div>
                <div class="auth-buttons">
                    <a href="Index.html" class="btn" id="logoutButton">Logout</a>
                    <a href="admin_login.php" class="btn">Admin</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Top Performers Section -->
    <section class="container">
        <h2 class="section-title">Top Performing Movies (Last 10 Days)</h2>
        <div class="movie-grid">
            <?php if (empty($movies)): ?>
                <p>No performance data available yet.</p>
            <?php else: ?>
                <?php foreach ($movies as $movie): ?>
                    <a href="movie.php?movie_id=<?= $movie['movie_id'] ?>" class="movie-card">
                        <div class="movie-image">
                        <img class="poster" src="<?php echo htmlspecialchars($movie['posters']); ?>" alt="Movie Poster">
                            <div class="movie-rating">
                                <?= number_format($movie['performance_score'], 1) ?>
                            </div>
                        </div>
                        <div class="movie-info">
                            <div class="movie-title"><?= htmlspecialchars($movie['movie_title']) ?></div>
                            <div class="movie-meta">
                                <span><?= date('Y', strtotime($movie['release_date'])) ?></span>
                                <span><?= htmlspecialchars($movie['genre']) ?></span>
                            </div>
                            <div class="performance-details">
                                <small>Ratings: <?= $movie['rating_count'] ?></small>
                                <small>Avg: <?= number_format($movie['avg_rating'], 1) ?>/5</small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer (same as your main site) -->
    <footer>
        <!-- Your existing footer content -->
    </footer>
</body>
</html>