<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$movie_id = intval($_GET['movie_id']);

// Fetch movie details
$movie_sql = $conn->prepare("SELECT * FROM movie WHERE movie_id = ?");
$movie_sql->bind_param("i", $movie_id);
$movie_sql->execute();
$movie_result = $movie_sql->get_result();

if ($movie_result->num_rows === 0) {
    die("Movie not found.");
}

$movie = $movie_result->fetch_assoc();
$movie_sql->close();

// Fetch all cast members for dropdown
$cast_sql = $conn->prepare("SELECT * FROM cast ORDER BY cast_name");
$cast_sql->execute();
$cast_result = $cast_sql->get_result();
$cast_members = $cast_result->fetch_all(MYSQLI_ASSOC);
$cast_sql->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $release_date = $_POST['release_date'];
    $genre = $_POST['genre'];
    $duration = intval($_POST['duration']);
    $cast_id = intval($_POST['cast_id']);
    $poster_url = $_POST['poster_url'];

    $update_sql = $conn->prepare("UPDATE movie SET 
                                movie_title = ?, 
                                release_date = ?, 
                                genera = ?, 
                                duration = ?, 
                                cast_id = ?,
                                posters = ?
                                WHERE movie_id = ?");
    $update_sql->bind_param("sssiisi", $title, $release_date, $genre, $duration, $cast_id, $poster_url, $movie_id);
    
    if ($update_sql->execute()) {
        $success = "Movie updated successfully!";
        // Refresh movie data
        $movie = [
            'movie_title' => $title,
            'release_date' => $release_date,
            'genera' => $genre,
            'duration' => $duration,
            'cast_id' => $cast_id,
            'posters' => $poster_url
        ];
    } else {
        $error = "Error updating movie: " . $conn->error;
    }
    $update_sql->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie - AfterStar Admin</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--dark);
            color: var(--light);
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: var(--dark);
            padding: 15px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #333;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        
        .logo span {
            color: var(--primary);
        }
        
        .back-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-btn:hover {
            background-color: #b2070f;
        }
        
        h1 {
            margin-bottom: 30px;
            color: white;
        }
        
        .form-container {
            background-color: var(--dark-gray);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--gray);
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #222;
            color: white;
            font-family: inherit;
        }
        
        .form-button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
        }
        
        .form-button:hover {
            background-color: #b2070f;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #2ecc71;
            color: white;
        }
        
        .alert-error {
            background-color: #e74c3c;
            color: white;
        }
        
        .poster-preview {
            max-width: 200px;
            margin-top: 10px;
            display: block;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="admin_panel.php" class="logo">After<span>Star</span> Admin</a>
            <a href="get_all_movies.php" class="back-btn">Back to Movies</a>
        </div>
    </header>
    
    <div class="container">
        <h1>Edit Movie: <?php echo htmlspecialchars($movie['movie_title']); ?></h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="title">Movie Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($movie['movie_title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" id="release_date" name="release_date" value="<?php echo htmlspecialchars($movie['release_date']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="genre">Genre</label>
                    <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($movie['genera']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="duration">Duration (minutes)</label>
                    <input type="number" id="duration" name="duration" min="1" value="<?php echo htmlspecialchars($movie['duration']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="cast_id">Main Cast Member</label>
                    <select id="cast_id" name="cast_id" required>
                        <option value="">Select Cast Member</option>
                        <?php foreach ($cast_members as $cast): ?>
                            <option value="<?php echo $cast['cast_id']; ?>" <?php echo ($cast['cast_id'] == $movie['cast_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cast['cast_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="poster_url">Poster URL</label>
                    <input type="text" id="poster_url" name="poster_url" value="<?php echo htmlspecialchars($movie['posters'] ?? ''); ?>">
                    <?php if (!empty($movie['posters'])): ?>
                        <img src="<?php echo htmlspecialchars($movie['posters']); ?>" alt="Poster Preview" class="poster-preview">
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="form-button">Update Movie</button>
            </form>
        </div>
    </div>
    
    <script>
        // Update poster preview when URL changes
        document.getElementById('poster_url').addEventListener('input', function() {
            const preview = document.querySelector('.poster-preview');
            if (preview) {
                preview.src = this.value;
            } else if (this.value) {
                const img = document.createElement('img');
                img.src = this.value;
                img.className = 'poster-preview';
                img.alt = 'Poster Preview';
                this.parentNode.appendChild(img);
            }
        });
    </script>
</body>
</html>