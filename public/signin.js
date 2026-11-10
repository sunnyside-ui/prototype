(() => {
    const button = document.querySelector('.password-toggle');
    const input = document.getElementById('signin-password');
    if (!button || !input) return;
    button.hidden = false;
    button.addEventListener('click', () => {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.textContent = show ? 'Hide' : 'Show';
        button.setAttribute('aria-pressed', String(show));
    });
})();
