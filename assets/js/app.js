$(document).ready(function() {
    // Sidebar toggle
    $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('show');
    });

    // Auto-hide sidebar on mobile after click
    if ($(window).width() < 768) {
        $('.nav-link').on('click', function() {
            if (!$(this).attr('data-bs-toggle')) {
                $('#sidebar').removeClass('show');
            }
        });
    }
});
