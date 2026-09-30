document.addEventListener('DOMContentLoaded', () => {
    const spotlightCarousel = document.querySelector('#spotlightCarousel');
    spotlightCarousel?.addEventListener('slide.bs.carousel', (event) => {
        spotlightCarousel.querySelectorAll('.spotlight-arrow-enter').forEach((slide) => slide.classList.remove('spotlight-arrow-enter'));
        if (event.relatedTarget) {
            requestAnimationFrame(() => event.relatedTarget.classList.add('spotlight-arrow-enter'));
        }
    });

    const mobileNavTrigger = document.querySelector('#mobileNavTrigger');
    const mobileNavDrawer = document.querySelector('#mobileNavigationDrawer');
    const mobileNavOverlay = document.querySelector('.mobile-navigation-overlay');
    const mobileNavClose = mobileNavDrawer?.querySelector('.mobile-drawer-close');
    const mobileBottomMenu = document.querySelector('#mobileBottomMenu');
    const mobileBottomSearch = document.querySelector('#mobileBottomSearch');
    const mobileSearchPanel = document.querySelector('#mobileSearchPanel');
    const mobileSearchClose = mobileSearchPanel?.querySelector('.mobile-search-close');
    const mobileSearchInput = mobileSearchPanel?.querySelector('input[name="q"]');

    if (mobileNavTrigger && mobileNavDrawer && mobileNavOverlay) {
        const setDrawerOpen = (open) => {
            document.body.classList.toggle('mobile-drawer-open', open);
            mobileNavDrawer.classList.toggle('is-open', open);
            mobileNavOverlay.classList.toggle('is-open', open);
            mobileNavTrigger.setAttribute('aria-expanded', String(open));
            mobileNavTrigger.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
            mobileBottomMenu?.setAttribute('aria-expanded', String(open));
            mobileBottomMenu?.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            mobileNavDrawer.setAttribute('aria-hidden', String(!open));
            mobileNavDrawer.inert = !open;
            mobileNavOverlay.hidden = !open;
        };

        mobileNavTrigger.addEventListener('click', () => {
            const open = mobileNavTrigger.getAttribute('aria-expanded') !== 'true';
            setDrawerOpen(open);
            if (open) mobileNavClose?.focus();
        });
        mobileBottomMenu?.addEventListener('click', () => {
            setDrawerOpen(mobileNavTrigger.getAttribute('aria-expanded') !== 'true');
        });
        mobileNavClose?.addEventListener('click', () => {
            setDrawerOpen(false);
            mobileNavTrigger.focus();
        });
        mobileNavOverlay.addEventListener('click', () => {
            setDrawerOpen(false);
            mobileNavTrigger.focus();
        });
        mobileNavDrawer.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setDrawerOpen(false));
        });
        mobileNavDrawer.querySelectorAll('.mobile-drawer-submenu-toggle').forEach((button) => {
            button.addEventListener('click', () => {
                const submenu = button.closest('.mobile-drawer-item')?.querySelector('.mobile-drawer-submenu');
                const open = button.getAttribute('aria-expanded') !== 'true';
                button.setAttribute('aria-expanded', String(open));
                button.setAttribute('aria-label', `${open ? 'Hide' : 'Show'} ${button.closest('.mobile-drawer-item')?.querySelector('.mobile-drawer-link')?.textContent.trim()} submenu`);
                submenu?.classList.toggle('is-open', open);
                if (submenu) {
                    submenu.setAttribute('aria-hidden', String(!open));
                    submenu.inert = !open;
                }
            });
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && mobileNavTrigger.getAttribute('aria-expanded') === 'true') {
                setDrawerOpen(false);
                mobileNavTrigger.focus();
            }
        });
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992 && mobileNavTrigger.getAttribute('aria-expanded') === 'true') setDrawerOpen(false);
        });
    }

    const setMobileSearchOpen = (open) => {
        if (!mobileSearchPanel || !mobileBottomSearch) return;
        mobileSearchPanel.hidden = !open;
        mobileSearchPanel.classList.toggle('is-open', open);
        mobileBottomSearch.setAttribute('aria-expanded', String(open));
        mobileBottomSearch.setAttribute('aria-label', open ? 'Close search' : 'Open search');
        if (open) {
            if (mobileNavTrigger?.getAttribute('aria-expanded') === 'true') mobileNavTrigger.click();
            mobileSearchInput?.focus();
        }
    };

    mobileBottomSearch?.addEventListener('click', () => {
        setMobileSearchOpen(mobileSearchPanel?.hidden ?? true);
    });
    mobileSearchClose?.addEventListener('click', () => setMobileSearchOpen(false));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mobileSearchPanel && !mobileSearchPanel.hidden) {
            setMobileSearchOpen(false);
            mobileBottomSearch?.focus();
        }
    });

    document.querySelectorAll('.modal[data-auto-show="true"]').forEach((element) => {
        bootstrap.Modal.getOrCreateInstance(element).show();
    });

    document.querySelectorAll('.nav-search[data-search-url]').forEach((searchForm) => {

    const searchInput = searchForm.querySelector('input[name="q"]');
    const results = searchForm.querySelector('.nav-search-results');
    let debounce;
    let activeRequest;

    const closeResults = () => {
        results.hidden = true;
        searchForm.classList.remove('has-results');
        searchInput.setAttribute('aria-expanded', 'false');
    };

    const showResults = (content) => {
        results.replaceChildren(content);
        results.hidden = false;
        searchForm.classList.add('has-results');
        searchInput.setAttribute('aria-expanded', 'true');
    };

    const makeMessage = (message) => {
        const element = document.createElement('div');
        element.className = 'nav-search-empty';
        element.textContent = message;
        return element;
    };

    searchInput.addEventListener('input', () => {
        window.clearTimeout(debounce);
        activeRequest?.abort();
        const term = searchInput.value.trim();
        if (term.length < 2) {
            closeResults();
            return;
        }

        showResults(makeMessage('Searching…'));
        debounce = window.setTimeout(async () => {
            activeRequest = new AbortController();
            try {
                const url = new URL(searchForm.dataset.searchUrl, window.location.origin);
                url.searchParams.set('q', term);
                const response = await fetch(url, {
                    headers: { Accept: 'application/json' },
                    signal: activeRequest.signal,
                });
                if (!response.ok) throw new Error('Search request failed');
                const stories = await response.json();
                if (searchInput.value.trim() !== term) return;
                if (!stories.length) {
                    showResults(makeMessage('No matching stories found.'));
                    return;
                }

                const fragment = document.createDocumentFragment();
                stories.forEach((story) => {
                    const link = document.createElement('a');
                    link.className = 'nav-search-result';
                    link.href = story.url;
                    link.setAttribute('role', 'option');

                    const image = document.createElement('img');
                    image.src = story.image;
                    image.alt = '';
                    image.loading = 'lazy';

                    const copy = document.createElement('span');
                    copy.className = 'nav-search-result-copy';
                    const category = document.createElement('span');
                    category.className = 'nav-search-result-category';
                    category.textContent = story.category;
                    const title = document.createElement('span');
                    title.className = 'nav-search-result-title';
                    title.textContent = story.title;

                    copy.append(category, title);
                    link.append(image, copy);
                    fragment.append(link);
                });
                showResults(fragment);
            } catch (error) {
                if (error.name !== 'AbortError') closeResults();
            }
        }, 200);
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeResults();
        if (event.key === 'ArrowDown') {
            const firstResult = results.querySelector('.nav-search-result');
            if (firstResult) {
                event.preventDefault();
                firstResult.focus();
            }
        }
    });

    document.addEventListener('click', (event) => {
        if (!searchForm.contains(event.target)) closeResults();
    });
    });
});
