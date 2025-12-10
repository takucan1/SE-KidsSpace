// Toggle header profile panel when user icon is clicked
(function(){
    const userBtn = document.getElementById('user-btn');
    const headerProfile = document.querySelector('.header .profile');
    const searchBtn = document.getElementById('search-btn');
    const searchForm = document.querySelector('.header .search-form');

    if(!userBtn || !headerProfile) return;

    // Toggle profile visibility
    userBtn.addEventListener('click', function(e){
        e.stopPropagation();
        headerProfile.classList.toggle('active');
        // hide search form if open (keeps UI tidy)
        if(searchForm) searchForm.classList.remove('active');
    });

    // Prevent clicks inside profile from closing it
    headerProfile.addEventListener('click', function(e){
        e.stopPropagation();
    });

    // Close profile when clicking elsewhere
    document.addEventListener('click', function(){
        headerProfile.classList.remove('active');
    });

    // If there's a search button (mobile), keep behavior consistent
    if(searchBtn && searchForm){
        searchBtn.addEventListener('click', function(e){
            e.stopPropagation();
            searchForm.classList.toggle('active');
            headerProfile.classList.remove('active');
        });
    }

    const toggleBtn = document.getElementById('toggle-btn');
    const bodyEl = document.body;

    // Theme toggle (dark / light) with persistence
    function applyTheme(theme){
        if(theme === 'dark'){
            bodyEl.classList.add('dark');
            if(toggleBtn){ toggleBtn.classList.remove('fa-sun'); toggleBtn.classList.add('fa-moon'); }
        } else {
            bodyEl.classList.remove('dark');
            if(toggleBtn){ toggleBtn.classList.remove('fa-moon'); toggleBtn.classList.add('fa-sun'); }
        }
    }

    // Initialize theme from localStorage or system preference
    (function initTheme(){
        const saved = localStorage.getItem('ks_theme');
        if(saved){
            applyTheme(saved);
        } else if(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches){
            applyTheme('dark');
        } else {
            applyTheme('light');
        }
    })();

    if(toggleBtn){
        toggleBtn.addEventListener('click', function(e){
            e.stopPropagation();
            const isDark = bodyEl.classList.contains('dark');
            const next = isDark ? 'light' : 'dark';
            applyTheme(next);
            localStorage.setItem('ks_theme', next);
        });
    }

})();
