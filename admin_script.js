// Check session on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch('check_admin_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                window.location.href = 'afterstar-website.html';
            }
        });
});

// Update logout function
function logout() {
    fetch('admin_logout.php')
        .then(() => {
            window.location.href = 'afterstar-website.html';
        });
}

// Rest of your existing admin_script.js code remains the same...
/// Update the Add Movie Form Handler
document.getElementById('addMovieForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const movieData = {
        title: document.getElementById('movieTitle').value,
        poster: document.getElementById('posterUrl').value,
        releaseDate: document.getElementById('releaseDate').value,
        genre: document.getElementById('genre').value,
        castId: document.getElementById('movieCast').value,
        duration: parseInt(document.getElementById('movieDuration').value)
    };

    // Validate duration
    if (isNaN(movieData.duration) || movieData.duration <= 0) {
        alert('Please enter a valid duration in minutes');
        return;
    }

    fetch('add_movie.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(movieData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Movie added successfully with duration and cast!');
            this.reset();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding movie');
    });
});

// Add Cast Form Handler - make sure this is the only version in your file
document.getElementById('addCastForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const castData = {
        name: document.getElementById('castName').value,
        role: document.getElementById('castRole').value,
        dob: document.getElementById('castDob').value || null // Handle empty date
    };

    // Simple validation
    if (!castData.name || !castData.role) {
        alert('Name and role are required');
        return;
    }

    fetch('add_cast.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(castData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cast member added successfully!');
            this.reset();
            loadCast(); // Refresh the cast dropdown in movie form
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding cast member');
    });
});

// Add this function to load cast members
function loadCast() {
    fetch('get_cast.php')
    .then(response => response.json())
    .then(cast => {
        const select = document.getElementById('movieCast');
        select.innerHTML = '<option value="">Select a cast member...</option>';
        
        cast.forEach(person => {
            const option = document.createElement('option');
            option.value = person.cast_id;
            option.textContent = person.cast_name;
            select.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error loading cast:', error);
    });
}

// Update the Add Movie Form Handler
document.getElementById('addMovieForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const movieData = {
        title: document.getElementById('movieTitle').value,
        poster: document.getElementById('posterUrl').value,
        releaseDate: document.getElementById('releaseDate').value,
        genre: document.getElementById('genre').value,
        castId: document.getElementById('movieCast').value
    };

    fetch('add_movie.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(movieData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Movie added with cast association!');
            this.reset();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding movie');
    });
});

// Initialize both movies and cast on page load
document.addEventListener('DOMContentLoaded', function() {
    loadMovies();  // For the cast association dropdown in add cast form
    loadCast();    // For the cast dropdown in add movie form
});

// Load movies for the cast association dropdown
function loadMovies() {
    fetch('get_movies.php')
    .then(response => response.json())
    .then(movies => {
        const select = document.getElementById('castMovie');
        select.innerHTML = '<option value="">Select a movie...</option>';
        
        movies.forEach(movie => {
            const option = document.createElement('option');
            option.value = movie.movie_id;
            option.textContent = movie.movie_title;
            select.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error loading movies:', error);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadMovies();
});


// Add this new function to handle association form
document.getElementById('associateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const associationData = {
        movieId: document.getElementById('associateMovie').value,
        castId: document.getElementById('associateCast').value
    };

    // Validate selection
    if (!associationData.movieId || !associationData.castId) {
        alert('Please select both a movie and a cast member');
        return;
    }

    fetch('add_association.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(associationData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Association created successfully!');
            this.reset();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating association');
    });
});

// Update the DOMContentLoaded event to load both movies and cast for the association form
document.addEventListener('DOMContentLoaded', function() {
    loadMovies();  // For the cast association dropdown in add cast form
    loadCast();    // For the cast dropdown in add movie form
    
    // Load movies and cast for the association form
    loadMoviesForAssociation();
    loadCastForAssociation();
});

// New function to load movies for association form
function loadMoviesForAssociation() {
    fetch('get_movies.php')
    .then(response => response.json())
    .then(movies => {
        const select = document.getElementById('associateMovie');
        select.innerHTML = '<option value="">Select a movie...</option>';
        
        movies.forEach(movie => {
            const option = document.createElement('option');
            option.value = movie.movie_id;
            option.textContent = movie.movie_title;
            select.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error loading movies:', error);
    });
}

// New function to load cast for association form
function loadCastForAssociation() {
    fetch('get_cast.php')
    .then(response => response.json())
    .then(cast => {
        const select = document.getElementById('associateCast');
        select.innerHTML = '<option value="">Select a cast member...</option>';
        
        cast.forEach(person => {
            const option = document.createElement('option');
            option.value = person.cast_id;
            option.textContent = `${person.cast_name} (${person.role})`;
            select.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error loading cast:', error);
    });
}

