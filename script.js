// // Update these modal functions in script.js
// function openModal(modalId) {
//     const modal = document.getElementById(modalId);
//     modal.style.display = 'flex';
//     modal.classList.add('modal-open');
//     document.body.style.overflow = 'hidden';
//     console.log(`Opening modal: ${modalId}`); // Debug log
// }

// function closeModal(modalId) {
//     const modal = document.getElementById(modalId);
//     modal.style.display = 'none';
//     modal.classList.remove('modal-open');
//     document.body.style.overflow = 'auto';
//     console.log(`Closing modal: ${modalId}`); // Debug log
// }

// function switchModal(closeModalId, openModalId) {
//     closeModal(closeModalId);
//     openModal(openModalId);
// }

// // Close modal when clicking outside of it
// window.onclick = function(event) {
//     if (event.target.classList.contains('modal')) {
//         closeModal(event.target.id);
//     }
// };



// Login form handler
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'login');

    fetch('db_operations.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeModal('loginModal');
            // Update UI to show logged in state
            document.getElementById('loggedOutButtons').style.display = 'none';
            document.getElementById('loggedInButtons').style.display = 'flex';
            document.getElementById('usernameDisplay').textContent = formData.get('username');
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error during login. Please try again.');
    });
});

// Register form handler
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'register');

    fetch('db_operations.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            switchModal('registerModal', 'loginModal');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error during registration. Please try again.');
    });
});




// Check login status on page load
function checkLoginStatus() {
    fetch('check_login.php')
    .then(response => response.json())
    .then(data => {
        if (data.loggedIn) {
            document.getElementById('loggedOutButtons').style.display = 'none';
            document.getElementById('loggedInButtons').style.display = 'flex';
            document.getElementById('usernameDisplay').textContent = data.username;
        } else {
            document.getElementById('loggedOutButtons').style.display = 'flex';
            document.getElementById('loggedInButtons').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error checking login status:', error);
    });
}

document.addEventListener('DOMContentLoaded', checkLoginStatus);


// Update the rating form handler
document.getElementById('ratingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        movieId: formData.get('movieId'),
        rating: formData.get('rating'),
        review: formData.get('review') || ''
    };

    // Validate
    if (!data.movieId || !data.rating) {
        alert('Please select a movie and provide a rating');
        return;
    }

    fetch('submit_rating.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            this.reset();
            // Reset star rating
            document.querySelectorAll('.star-rating input').forEach(radio => {
                radio.checked = false;
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting rating');
    });
});



// Load movies for rating form
function loadMoviesForRating() {
    fetch('get_movies.php')
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(movies => {
        const select = document.getElementById('selectMovie');
        select.innerHTML = '<option value="">Choose a movie...</option>';
        
        if (movies.length === 0) {
            console.warn('No movies found in database');
            return;
        }
        
        movies.forEach(movie => {
            const option = document.createElement('option');
            option.value = movie.movie_id;
            option.textContent = movie.movie_title;
            select.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error loading movies:', error);
        alert('Error loading movies. Please try again later.');
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', loadMoviesForRating);










