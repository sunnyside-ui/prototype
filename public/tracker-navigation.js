const navigation = document.getElementById('tracker-navigation');
let pending;

navigation?.addEventListener('click', async (event) => {
    const link = event.target.closest('a');
    if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;

    event.preventDefault();
    pending?.abort();
    const request = new AbortController();
    pending = request;
    navigation.setAttribute('aria-busy', 'true');

    try {
        const url = new URL(link.href);
        url.hash = '';
        const response = await fetch(url, { signal: request.signal });
        if (!response.ok) throw new Error('Could not load term');

        const page = new DOMParser().parseFromString(await response.text(), 'text/html');
        const nextNavigation = page.getElementById('tracker-navigation');
        const nextPanel = page.getElementById('tracker-term-panel');
        if (!nextNavigation || !nextPanel) throw new Error('Tracker unavailable');

        const rows = Array.from(navigation.querySelectorAll('nav'));
        const offsets = rows.map((row) => row.scrollLeft);
        const rowIndex = rows.indexOf(link.closest('nav'));
        navigation.replaceChildren(...nextNavigation.childNodes);
        document.getElementById('tracker-term-panel').replaceWith(nextPanel);
        const newRows = navigation.querySelectorAll('nav');
        newRows.forEach((row, index) => { row.scrollLeft = offsets[index]; });
        newRows[rowIndex]?.querySelector('[aria-current]')?.focus({ preventScroll: true });
        history.replaceState(null, '', url);
    } catch (error) {
        if (error.name !== 'AbortError') window.location.assign(link.href);
    } finally {
        if (pending === request) navigation.removeAttribute('aria-busy');
    }
});
