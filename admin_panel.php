<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - AfterStar</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Admin Header -->
    <header>
        <div class="container">
            <nav>
                <a href="#" class="logo">After<span>Star</span> Admin</a>
                <div class="nav-links">
                    <a href="#movies">Manage Movies</a>
                    <a href="#cast">Manage Cast</a>
                    <a href="#associations">Manage Associations</a>
                    <a href="#ratings">View All Movies</a>
                </div>
                <div class="auth-buttons">
                    <button onclick="logout()">Logout</button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Admin Content -->
    <!-- In the Add New Movie form section -->
    <section class="container" id="movies">
        <h2 class="section-title">Add New Movie</h2>
        <div class="form-container">
            <form id="addMovieForm">
                <div class="form-group">
                    <label for="movieTitle">Movie Title</label>
                    <input type="text" id="movieTitle" required>
                </div>

                <div class="form-group">
                    <label for="posterUrl">Poster URL</label>
                    <input type="text" id="posterUrl">
                </div>

                <div class="form-group">
                    <label for="releaseDate">Release Date</label>
                    <input type="date" id="releaseDate" required>
                </div>

                <div class="form-group">
                    <label for="genre">Genre</label>
                    <input type="text" id="genre" required>
                </div>
                
                <!-- In the Add Movie form -->
                <div class="form-group">
                    <label for="movieDuration">Duration (minutes)</label>
                    <input type="number" id="movieDuration" min="1" required>
                </div>

                <!-- In the Add Movie form -->
                <div class="form-group">
                    <label for="movieCast">Main Cast Member</label>
                    <select id="movieCast" required>
                        <option value="">Select a cast member...</option>
                        <!-- Cast options will be populated by JavaScript -->
                    </select>
                </div>

                <button type="submit" class="form-button">Add Movie</button>
            </form>
        </div>
    </section>

    <section class="container" id="cast">
        <h2 class="section-title">Add New Cast Member</h2>
        <div class="form-container">
            <form id="addCastForm">
                <div class="form-group">
                    <label for="castName">Name</label>
                    <input type="text" id="castName" required>
                </div>

                <div class="form-group">
                    <label for="castRole">Role</label>
                    <input type="text" id="castRole" required>
                </div>

                <div class="form-group">
                    <label for="castDob">Date of Birth</label>
                    <input type="date" id="castDob">
                </div>
                
                <!--div class="form-group">
                    <label for="castMovie">Associated Movie</label>
                    <select id="castMovie" required>
                        <option value="">Select a movie...</option-->
                        <!-- Movies will be populated via JavaScript -->
                    <!--/select>
                </div-->

                <button type="submit" class="form-button">Add Cast Member</button>
            </form>
        </div>
    </section>

    <!-- Add this new section after the existing sections -->
    <section class="container" id="associations">
        <h2 class="section-title">Associate Movie with Cast</h2>
        <div class="form-container">
            <form id="associateForm">
                <div class="form-group">
                    <label for="associateMovie">Select Movie</label>
                    <select id="associateMovie" required>
                        <option value="">Select a movie...</option>
                        <!-- Movies will be populated via JavaScript -->
                    </select>
                </div>
    
                <div class="form-group">
                    <label for="associateCast">Select Cast Member</label>
                    <select id="associateCast" required>
                        <option value="">Select a cast member...</option>
                        <!-- Cast will be populated via JavaScript -->
                    </select>
                </div>
    
                <button type="submit" class="form-button">Create Association</button>
            </form>
        </div>
        </section>
        
        <!-- section for viewing All Movies Button -->
        <section class="container" id="ratings">
            <div style="text-align: center;">
                <button onclick="window.location.href='get_all_movies.php'" class="btn" 
                        style="margin: 20px 0; padding: 12px 30px; font-size: 18px;">
                    Show All Movies
                </button>
            </div>
        </section>
        <script src="admin_script.js"></script>
        </body>

</html>