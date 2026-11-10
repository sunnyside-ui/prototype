const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const { runInNewContext } = require('node:vm');
const script = readFileSync('public/navigation.js', 'utf8');

function navigation(matches, stored, unavailable = false) {
    const events = {};
    const menu = { open: true, scrollTop: 0, querySelector: () => null,
        addEventListener: (name, callback) => { events[name] = callback; } };
    const rail = { dataset: { navigationKey: '1-student' }, querySelector: () => menu,
        addEventListener: (name, callback) => { events[name] = callback; } };
    const desktop = { matches, addEventListener: (_, callback) => { events.resize = callback; } };
    let saved = stored;
    runInNewContext(script, {
        document: { querySelector: () => rail },
        window: { matchMedia: () => desktop, addEventListener: (name, callback) => { events[name] = callback; } },
        sessionStorage: {
            getItem: () => { if (unavailable) throw Error('Storage blocked'); return saved; },
            setItem: (_, value) => { if (unavailable) throw Error('Storage blocked'); saved = value; },
        },
    });
    return { menu, desktop, events, saved: () => JSON.parse(saved) };
}

test('restores desktop menu scroll and saves the next position', () => {
    const nav = navigation(true, '{"top":360,"menuOpen":false}');
    assert.equal(nav.menu.open, true);
    assert.equal(nav.menu.scrollTop, 360);
    nav.menu.scrollTop = 440;
    nav.events.pagehide();
    assert.equal(nav.saved().top, 440);
    assert.equal(nav.saved().menuOpen, false);
});

test('restores mobile disclosure without losing desktop position', () => {
    const nav = navigation(false, '{"top":360,"menuOpen":true}');
    assert.equal(nav.menu.open, true);
    nav.menu.open = false;
    nav.events.toggle();
    nav.desktop.matches = true;
    nav.events.resize();
    assert.equal(nav.menu.open, true);
    assert.equal(nav.menu.scrollTop, 360);
    nav.desktop.matches = false;
    nav.events.resize();
    assert.equal(nav.menu.open, false);
});

test('defaults mobile menu to closed with missing, invalid or blocked storage', () => {
    for (const stored of [null, 'invalid']) {
        assert.equal(navigation(false, stored).menu.open, false);
    }
    const nav = navigation(false, null, true);
    assert.equal(nav.menu.open, false);
    assert.doesNotThrow(() => nav.events.pagehide());
});
