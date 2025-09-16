<?php
// PHP code to fetch movies
header('Content-Type: text/html; charset=UTF-8');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'after_stars_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = $conn->prepare("DELETE FROM movie WHERE movie_id = ?");
    $delete_sql->bind_param("i", $delete_id);
    
    if ($delete_sql->execute()) {
        $delete_message = "Movie deleted successfully!";
    } else {
        $delete_message = "Error deleting movie: " . $conn->error;
    }
    $delete_sql->close();
    
    // Refresh the page to show updated list
    header("Location: get_all_movies.php?message=" . urlencode($delete_message));
    exit();
}

$query = "SELECT m.movie_id, m.movie_title AS title, m.release_date, 
            m.genera AS genre,m.duration AS duration,
        GROUP_CONCAT(c.cast_name SEPARATOR ', ') AS cast_members,   m.cast_id
    FROM movie m
    LEFT JOIN cast c ON m.cast_id = c.cast_id
    GROUP BY m.movie_id
    ORDER BY m.movie_id";
    
$result = $conn->query($query);
$movies = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['release_date'] = date('d M Y', strtotime($row['release_date']));
        $movies[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Movies - AfterStar Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #222;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        
        .logo span {
            color: #e50914;
        }
        
        .back-btn {
            background-color: #e50914;
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
            margin: 30px 0;
            color: #222;
        }
        
        .movies-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .movies-table th, .movies-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .movies-table th {
            background-color: #f8f8f8;
            font-weight: 600;
            color: #555;
        }
        
        .movies-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .movie-poster {
            width: 60px;
            height: 90px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .no-movies {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }
        
        .action-btn {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin: 2px;
        }
        
        .edit-btn {
            background-color: #3498db;
        }
        
        .edit-btn:hover {
            background-color: #2980b9;
        }
        
        .delete-btn {
            background-color: #e74c3c;
        }
        
        .delete-btn:hover {
            background-color: #c0392b;
        }
        
        .action-btns {
            display: flex;
            gap: 5px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: white;
        }
        
        .alert-success {
            background-color: #2ecc71;
        }
        
        .alert-error {
            background-color: #e74c3c;
        }
        
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 10px;
            }
            
            .movies-table {
                display: block;
                overflow-x: auto;
            }
            
            .movies-table th, .movies-table td {
                padding: 10px;
            }
            
            .action-btns {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <a href="admin_panel.php" class="logo">After<span>Star</span> Admin</a>
            <a href="admin_panel.php" class="back-btn">Back to Admin Panel</a>
        </div>
    </header>
    
    <div class="container">
        <h1>All Movies in Database</h1>
        
        <?php if (isset($_GET['message'])): ?>
            <div class="alert <?php echo strpos($_GET['message'], 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($movies)): ?>
            <div class="no-movies">
                <p>No movies found in the database.</p>
            </div>
        <?php else: ?>
            <table class="movies-table">
                <thead>
                    <tr>
                        <th>Movie ID</th>
                        <th>Title</th>
                        <th>Release Date</th>
                        <th>Genre</th>
                        <th>Duration (min)</th>
                        <th>Cast Members</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movies as $movie): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($movie['movie_id']); ?></td>
                            <td><?php echo htmlspecialchars($movie['title']); ?></td>
                            <td><?php echo htmlspecialchars($movie['release_date']); ?></td>
                            <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                            <td><?php echo htmlspecialchars($movie['duration']); ?></td>
                            <td><?php echo htmlspecialchars($movie['cast_members'] ?? 'Not specified'); ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit_movie.php?movie_id=<?php echo htmlspecialchars($movie['movie_id']); ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="get_all_movies.php?delete_id=<?php echo htmlspecialchars($movie['movie_id']); ?>" 
                                       class="action-btn delete-btn"
                                       onclick="return confirm('Are you sure you want to delete this movie?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <script>
        // JavaScript for additional functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to rows if you want to make them clickable
            const rows = document.querySelectorAll('.movies-table tbody tr');
            rows.forEach(row => {
                row.addEventListener('click', function(e) {
                    // Don't trigger row click if clicking on action buttons
                    if (!e.target.closest('.action-btn')) {
                        // You can add functionality to edit movies when clicked
                        console.log('Row clicked');
                    }
                });
            });
        });
    </script>
</body>
</html>