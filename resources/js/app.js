import 'bootstrap';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const headers = {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    };

    const htmlEl = document.documentElement;
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme) htmlEl.setAttribute('data-bs-theme', storedTheme);

    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const newTheme = htmlEl.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            htmlEl.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }

    function showMatchNotification(name) {
        const alert = document.getElementById('matchNotification');
        if (!alert) return;
        alert.textContent = `¡Enhorabuena! Has hecho match con ${name}.`;
        alert.classList.remove('d-none');
        setTimeout(() => alert.classList.add('d-none'), 4000);
    }

    function processForm(form) {
        axios.post(form.action, new FormData(form), { headers })
            .then(response => {
                if (response.data.is_mutual_match && response.data.other_user) {
                    showMatchNotification(response.data.other_user.name);
                }

                const card = form.closest('.col-md-4');
                if (card) card.remove();
            })
            .catch(error => console.error(error));
    }

    document.body.addEventListener('submit', e => {
        if (e.target.matches('.like-form') || e.target.matches('.dislike-form')) {
            e.preventDefault();
            processForm(e.target);
        }
    });
});
