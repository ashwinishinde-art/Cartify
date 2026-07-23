document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('reviewsTrack');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const originalCards = Array.from(track.children);
    const totalOriginals = originalCards.length;
    
    // 1. Dynamic Infinite Setup: Double clone sets to cover both left and right directions
    // Append a copy set to the end
    originalCards.forEach(card => {
        track.appendChild(card.cloneNode(true));
    });
    // Prepend a copy set to the beginning
    for (let i = totalOriginals - 1; i >= 0; i--) {
        track.insertBefore(originalCards[i].cloneNode(true), track.firstChild);
    }

    // Since a full set of 6 clones was prepended, our real first card starts at index equal to totalOriginals
    let currentIndex = totalOriginals;
    let isTransitioning = false;

    function getVisibleCardsCount() {
        if (window.innerWidth <= 650) return 1;
        if (window.innerWidth <= 1024) return 2;
        return 3;
    }

    function getLayoutMetrics() {
        const cardWidth = track.children[0].getBoundingClientRect().width;
        const gap = 24; // Must match the CSS gap property setting exactly
        return { cardWidth, gap };
    }

    function updatePosition(animate = true) {
        const { cardWidth, gap } = getLayoutMetrics();
        if (animate) {
            track.style.transition = "transform 0.5s cubic-bezier(0.25, 1, 0.5, 1)";
        } else {
            track.style.transition = "none";
        }
        const offset = currentIndex * (cardWidth + gap);
        track.style.transform = `translateX(-${offset}px)`;
    }

    // 2. Teleportation Engine Checks: Snaps positions invisibly at boundaries without layout flashing
    track.addEventListener('transitionend', () => {
        isTransitioning = false;
        
        // If scrolled past rightmost original items into right clones
        if (currentIndex >= totalOriginals * 2) {
            currentIndex = currentIndex - totalOriginals;
            updatePosition(false);
        }
        
        // If scrolled past leftmost original items into left clones
        if (currentIndex < totalOriginals) {
            currentIndex = currentIndex + totalOriginals;
            updatePosition(false);
        }
    });

    // 3. Navigation Click Trigger Control Modules
    nextBtn.addEventListener('click', () => {
        if (isTransitioning) return;
        isTransitioning = true;
        currentIndex += getVisibleCardsCount();
        updatePosition(true);
    });

    prevBtn.addEventListener('click', () => {
        if (isTransitioning) return;
        isTransitioning = true;
        currentIndex -= getVisibleCardsCount();
        updatePosition(true);
    });

    // Recalculates position values safely if browser boundaries change dimensions
    window.addEventListener('resize', () => {
        updatePosition(false);
    });

    // Bootstrap initial operational state setup
    // Timeout gives structural flex renderings a microsecond to lock dimensions before reading widths
    setTimeout(() => {
        updatePosition(false);
    }, 50);
});

document.getElementById('searchTriggerBtn').addEventListener('click', function(e) {
    const wrapper = document.getElementById('searchInputWrapper');
    const input = document.getElementById('searchInput');
    const form = document.getElementById('searchForm');
    const links = document.getElementById('standardLinks');

    if (!wrapper.classList.contains('active')) {
        // Slide search bar open and temporarily fade regular menu links
        wrapper.classList.add('active');
        links.style.opacity = '0.3';
        input.focus();
    } else {
        // If there's text inside, execute the search
        if (input.value.trim() !== "") {
            form.submit();
        } else {
            // Otherwise, slide closed and return links to normal
            wrapper.classList.remove('active');
            links.style.opacity = '1';
        }
    }
});

// Close search bar if user clicks outside of header
document.addEventListener('click', function(e) {
    const container = document.querySelector('.nav-menu-wrapper');
    const trigger = document.getElementById('searchTriggerBtn');
    const wrapper = document.getElementById('searchInputWrapper');
    const links = document.getElementById('standardLinks');
    
    if (!container.contains(e.target) && !trigger.contains(e.target)) {
        wrapper.classList.remove('active');
        links.style.opacity = '1';
    }
});
