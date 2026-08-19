function validateAuth(event, type) {
    // 1. Stop the form from submitting immediately
    event.preventDefault();
    
    // 2. Find the Enrollment ID input box on the page
    const idInput = document.getElementById('enrollment');
    if (!idInput) return; // If it doesn't exist, do nothing
    
    // 3. Get the text the user typed and remove any extra spaces
    const studentId = idInput.value.trim();
    
    // 4. The Regular Expression (Regex) that enforces your rule
    const regex = /^\d{2}[a-zA-Z]{3}\d{3}$/;
    
    // 5. Test if the typed ID matches the Regex rule
    if (regex.test(studentId)) {
        // If it matches, show the success message
        if (type === 'login') {
            alert("successfull login");
        } else {
            alert("successfull register");
        }
        // Redirect the user to the home page (index.html)
        window.location.href = 'index.html';
    } else {
        // If it DOES NOT match, show the error message
        alert("invalid credentials");
    }
}


function toggleDarkMode() {
    const isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
}

// Apply dark mode on page load
document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }
});
