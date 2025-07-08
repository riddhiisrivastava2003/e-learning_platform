document.addEventListener('DOMContentLoaded', function() {
    const html = document.documentElement;
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    // Load saved theme or system preference
    let theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    setTheme(theme);
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            theme = (html.getAttribute('data-theme') === 'dark') ? 'light' : 'dark';
            setTheme(theme);
            localStorage.setItem('theme', theme);
        });
    }
    function setTheme(theme) {
        html.setAttribute('data-theme', theme);
        if (themeIcon) {
            themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            themeToggle.title = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        }
    }
}); 