function togglePassword(btn) {
    const input = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    const isHidden = input.type === 'password';

    input.type = isHidden ? 'text' : 'password';

    icon.innerHTML = isHidden ?
        `<path d="M13.35 13.35A4 4 0 018.65 8.65M1 8s2.5-5 7-5c1.38 0 2.63.37 3.72.97M15.28 10.28C15.73 9.57 16 8.82 16 8c0 0-2.16-5-7-5" /><line x1="2" y1="2" x2="14" y2="14" stroke-linecap="round" />` :
        `<path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z" /><circle cx="8" cy="8" r="2" />`;
}