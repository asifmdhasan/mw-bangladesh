(() => {
    const box = document.querySelector('.cur[data-cursor="ripple"]');
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (!box || !finePointer.matches || reducedMotion.matches) return;

    const interactiveSelector = 'a[href], button:not(:disabled), [role="button"], [data-cursor="ripple"]';
    let lastX = 0;
    let lastY = 0;
    let hasPosition = false;
    let idleTimer;

    const set = (property, value) => box.style.setProperty(property, value);
    const leaveActiveArea = () => {
        document.body.classList.remove('custom-cursor-active');
        box.classList.remove('is-active', 'is-near', 'is-interactive', 'is-ripple');
        set('--speed', '0');
        hasPosition = false;
        window.clearTimeout(idleTimer);
    };

    document.addEventListener('pointermove', (event) => {
        if (event.pointerType !== 'mouse' && event.pointerType !== 'pen') return;

        const target = event.target instanceof Element ? event.target : null;
        if (!target || target.closest('header.site-header')) {
            leaveActiveArea();
            return;
        }

        const x = event.clientX;
        const y = event.clientY;

        if (hasPosition) {
            const dx = x - lastX;
            const dy = y - lastY;
            const distance = Math.hypot(dx, dy);

            if (distance > .5) set('--angle', `${Math.atan2(dy, dx).toFixed(3)}rad`);
            set('--speed', Math.min(1, distance / 32).toFixed(3));
        } else {
            set('--speed', '0');
        }

        set('--x', `${x.toFixed(1)}px`);
        set('--y', `${y.toFixed(1)}px`);
        box.classList.add('is-active');
        document.body.classList.add('custom-cursor-active');

        const interactive = target.closest(interactiveSelector);
        box.classList.toggle('is-interactive', Boolean(interactive));
        box.classList.toggle('is-ripple', Boolean(interactive));

        lastX = x;
        lastY = y;
        hasPosition = true;

        window.clearTimeout(idleTimer);
        idleTimer = window.setTimeout(() => set('--speed', '0'), 120);
    }, { passive: true });

    window.addEventListener('blur', leaveActiveArea);
    document.documentElement.addEventListener('pointerleave', leaveActiveArea, { passive: true });
})();
