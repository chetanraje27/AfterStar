<?php
// Connect to DB
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search query
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Prepare SQL query to search in movie table
$sql = "SELECT * FROM movie WHERE 
        movie_title LIKE ? OR 
        genera LIKE ? OR
        movie_id IN (SELECT movie_id FROM movie_cast WHERE cast_id IN 
                    (SELECT cast_id FROM cast WHERE cast_name LIKE ?))";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$searchTerm = "%" . $query . "%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - AfterStar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header (same as your home page) -->
    <header>
        <div class="container">
            <nav>
                <a href="afterstar-website.html" class="logo">After<span>Star</span></a>
            </nav>
        </div>
    </header>

    <!-- Search Results Section -->
    <section class="container">
        <h2 class="section-title">Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="movie-grid">
                <?php while ($movie = $result->fetch_assoc()): ?>
                    <a href="movie.php?movie_id=<?php echo $movie['movie_id']; ?>" class="movie-card">
                        <div class="movie-image">
                            <?php if (!empty($movie['posters'])): ?>
                                <img src="<?php echo htmlspecialchars($movie['posters']); ?>" alt="<?php echo htmlspecialchars($movie['movie_title']); ?>">
                            <?php else: ?>
                                <div style="background:#333;width:100%;height:100%;"></div>
                            <?php endif; ?>
                            <div class="movie-rating"><?php echo number_format($movie['rating'], 1); ?></div>
                        </div>
                        <div class="movie-info">
                            <div class="movie-title"><?php echo htmlspecialchars($movie['movie_title']); ?></div>
                            <div class="movie-meta">
                                <span><?php echo substr($movie['release_date'], 0, 4); ?></span>
                                <span><?php echo htmlspecialchars($movie['genera']); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; margin: 40px 0; font-size: 18px;">
                No movies found matching your search. Try different keywords.
            </p>
        <?php endif; ?>
    </section>

    <!-- Footer (same as your home page) -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>AfterStar</h3>
                    <p>Discover, rate, and find your next favorite movie with our personalized recommendations.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>