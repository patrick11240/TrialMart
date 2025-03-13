document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleBtn = document.getElementById('toggle-sidebar');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    });

    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const themeIcon = themeToggle.querySelector('i');

    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        if (body.classList.contains('dark-mode')) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    });

    // Navigation Active State
    const navLinks = document.querySelectorAll('.nav-links li');
    
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navLinks.forEach(item => item.classList.remove('active'));
            link.classList.add('active');
        });
    });

    // Responsive Sidebar
    function handleResize() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize(); // Initial check
});
