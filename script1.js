document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } else {
            alert(data.message || 'Login failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Login error occurred');
    });
});



document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (document.getElementById('password').value.length < 6) {
        alert('Password must be at least 6 characters');
        return;
    }
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } else {
            alert(data.message || 'Registration failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Registration error occurred');
    });
});