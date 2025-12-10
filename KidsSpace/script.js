function showForm(formId) {
    document.querySelectorAll('.form-box').forEach(form => form.classList.remove("active"));
    document.getElementById(formId).classList.add("active");
}

document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('menu-btn');
    const sideBar = document.querySelector('.side-bar');
    const body = document.body;

    if (menuBtn && sideBar) {
        menuBtn.addEventListener('click', function() {
            sideBar.classList.toggle('active');
            body.classList.toggle('sidebar-active');
        });
    }

    // Profile dropdown toggle
    const userBtn = document.getElementById('user-btn');
    const profile = document.querySelector('.header .profile');

    if (userBtn && profile) {
        userBtn.addEventListener('click', function() {
            profile.classList.toggle('active');
        });
    }

    // Search form toggle
    const searchBtn = document.getElementById('search-btn');
    const searchForm = document.querySelector('.header .search-form');

    if (searchBtn && searchForm) {
        searchBtn.addEventListener('click', function() {
            searchForm.classList.toggle('active');
        });
    }
});
