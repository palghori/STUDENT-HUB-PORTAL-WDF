function validateAuth(event, type) {
    event.preventDefault();
    
    const idInput = document.getElementById('enrollment');
    if (!idInput) return; 
    
    const studentId = idInput.value.trim();
    const regex = /(^\d{2}[a-zA-Z]{3}\d{3}$)|(^D2D[a-zA-Z]{2,3}\d{3}$)/i;
    
    if (!regex.test(studentId)) {
        alert("Invalid Enrollment ID! It should be like 25DCE030 or D2DCE030.");
        return;
    }
    
    if (type === 'register') {
        const name = document.getElementById('name').value.trim();
        const mobile = document.getElementById('mobile').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        const nameRegex = /^[a-zA-Z\s]+$/;
        if (!nameRegex.test(name)) {
            alert("Invalid Name! Only letters and spaces are allowed.");
            return;
        }
        
        const mobileRegex = /^\d{10}$/;
        if (!mobileRegex.test(mobile)) {
            alert("Invalid Mobile Number! It must be exactly 10 digits.");
            return;
        }
        
        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            return;
        }
        
        alert("Successful register!");
    } else {
        alert("Successful login!");
    }
    
    window.location.href = 'index.html';
}
function toggleDarkMode() {
    const isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
}
document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }
});
