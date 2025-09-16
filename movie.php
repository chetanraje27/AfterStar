<?php
// Connect to DB
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if movie_id is set
if (!isset($_GET['movie_id']) || !is_numeric($_GET['movie_id'])) {
    die("Invalid movie ID.");
}

// Get movie ID
$movie_id = intval($_GET['movie_id']);

// Fetch movie details
$sql = $conn->prepare("SELECT * FROM movie WHERE movie_id = ?");
$sql->bind_param("i", $movie_id);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    $movie = $result->fetch_assoc();
} else {
    echo "Movie not found!";
    exit;
}

// Fetch cast details
$cast_sql = $conn->prepare("SELECT * FROM cast WHERE cast_id = ?");
$cast_sql->bind_param("i", $movie['cast_id']);
$cast_sql->execute();
$cast_result = $cast_sql->get_result();

if ($cast_result->num_rows > 0) {
    $cast = $cast_result->fetch_assoc();
} else {
    $cast = null;
}

// get avrage rating from rating table
$rating_sql = $conn->prepare("SELECT AVG(rating) as average_rating FROM ratings WHERE movie_id = ?");
$rating_sql->bind_param("i", $movie_id);
$rating_sql->execute();
$rating_result = $rating_sql->get_result();
$rating = $rating_result->fetch_assoc();
$average_rating = $rating['average_rating'] ? round($rating['average_rating'], 1) : 'No ratings yet';

$rating_sql->close();
$sql->close();
$cast_sql->close();

// Fetch reviews from ratings table
$reviews_sql = $conn->prepare("SELECT rating, review, created_at FROM ratings WHERE movie_id = ? AND review IS NOT NULL AND review != '' ORDER BY created_at DESC");
$reviews_sql->bind_param("i", $movie_id);
$reviews_sql->execute();
$reviews_result = $reviews_sql->get_result();
$reviews = [];

if ($reviews_result->num_rows > 0) {
    while ($row = $reviews_result->fetch_assoc()) {
        $reviews[] = $row;
    }
}

$reviews_sql->close();

$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($movie['movie_title']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #e50914;
            --dark: #141414;
            --light: #f5f5f5;
            --gray: #808080;
            --dark-gray: #2a2a2a;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            line-height: 1.6;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .movie-header {
            display: flex;
            flex-direction: column;
            position: relative;
            margin-bottom: 30px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .backdrop {
            position: relative;
            width: 100%;
            height: 400px;
            background: linear-gradient(to right, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.3) 100%);
        }
        
        .backdrop::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, var(--dark) 0%, transparent 100%);
        }
        
        .backdrop-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.5;
        }
        
        .movie-content {
            position: absolute;
            bottom: 0;
            left: 0;
            padding: 30px;
            width: 100%;
            display: flex;
            align-items: flex-end;
            gap: 30px;
        }
        
        .poster-container {
            flex-shrink: 0;
            width: 250px;
            height: 375px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
            transform: translateY(50px);
            transition: transform 0.3s ease;
        }
        
        .poster {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .movie-info {
            flex-grow: 1;
        }
        
        .movie-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .meta-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }
        
        .rating {
            background-color: var(--primary);
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .star-icon {
            color: gold;
        }
        
        .section {
            background-color: var(--dark-gray);
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: white;
            position: relative;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--primary);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .label {
            font-weight: 600;
            color: var(--gray);
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .value {
            font-weight: 500;
            color: white;
            font-size: 1rem;
        }
        
        .cast-container {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 20px;
        }
        
        .cast-card {
            background-color: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            width: 150px;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }
        
        .cast-card:hover {
            transform: translateY(-5px);
        }
        
        .cast-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .cast-info {
            padding: 15px;
        }
        
        .cast-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: white;
        }
        
        .cast-role {
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        @media (max-width: 768px) {
            .movie-content {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .poster-container {
                width: 150px;
                height: 225px;
                transform: none;
                margin-bottom: 20px;
            }
            
            .movie-title {
                font-size: 1.8rem;
            }
        }

        .reviews-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .review-card {
            background-color: #1a1a1a;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #333;
        }

        .review-rating {
            color: gold;
            font-size: 1.2rem;
            letter-spacing: 2px;
        }

        .review-date {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .review-content {
            line-height: 1.6;
            color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="movie-header">
            <div class="backdrop">
             
            </div>
            <div class="movie-content">
                <?php if (!empty($movie['posters'])): ?>
                    <div class="poster-container">
                        <img class="poster" src="<?php echo htmlspecialchars($movie['posters']); ?>" alt="Movie Poster">
                    </div>
                <?php endif; ?>
                <div class="movie-info">
                    <h1 class="movie-title"><?php echo htmlspecialchars($movie['movie_title']); ?></h1>
                    <div class="meta-info">
                        <span class="meta-item"><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($movie['release_date']); ?></span>
                        <span class="meta-item"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($movie['duration']); ?> min</span>
                        <span class="rating">
                            <i class="fas fa-star star-icon"></i>
                            <?php echo htmlspecialchars($average_rating); ?>
                        </span>
                        <span class="meta-item"><i class="fas fa-film"></i> <?php echo htmlspecialchars($movie['genera']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">Movie Details</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Release Date</span>
                    <span class="value"><?php echo htmlspecialchars($movie['release_date']); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Duration</span>
                    <span class="value"><?php echo htmlspecialchars($movie['duration']); ?> minutes</span>
                </div>
                <div class="info-item">
                    <span class="label">Genre</span>
                    <span class="value"><?php echo htmlspecialchars($movie['genera']); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">User Rating</span>
                    <span class="value">
                        <i class="fas fa-star" style="color: gold;"></i> 
                        <?php echo htmlspecialchars($average_rating); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <?php if ($cast): ?>
            <div class="section">
                <h2 class="section-title">Cast</h2>
                <div class="cast-container">
                    <div class="cast-card">
                        <div class="cast-img" style="background-color: #333; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="font-size: 3rem; color: #666;"></i>
                        </div>
                        <div class="cast-info">
                            <h3 class="cast-name"><?php echo htmlspecialchars($cast['cast_name']); ?></h3>
                            <p class="cast-role"><?php echo htmlspecialchars($cast['role']); ?></p>
                            <p class="cast-role">Born: <?php echo htmlspecialchars($cast['dob']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="section">
                <h2 class="section-title">Cast</h2>
                <p style="color: var(--gray);">No cast information available.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($reviews)): ?>
    <div class="section">
        <h2 class="section-title">User Reviews</h2>
        <div class="reviews-container">
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-rating">
                            <?php echo str_repeat('★', $review['rating']); ?>
                            <?php echo str_repeat('☆', 5 - $review['rating']); ?>
                        </div>
                        <div class="review-date">
                            <?php echo date('M d, Y h:i A', strtotime($review['created_at'])); ?>
                        </div>
                    </div>
                    <div class="review-content">
                        <?php echo nl2br(htmlspecialchars($review['review'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

    </div>
</body>
</html>