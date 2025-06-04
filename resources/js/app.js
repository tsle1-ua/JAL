import 'bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

document.addEventListener('DOMContentLoaded', () => {
    const messages = document.getElementById('messages');
    const form = document.getElementById('message-form');
    if (messages && form) {
        const matchId = form.action.split('/').pop();
        window.Echo.private('match.' + matchId)
            .listen('MessageSent', (e) => {
                const div = document.createElement('div');
                div.classList.add('mb-2');
                div.innerHTML = `<strong>${e.message.sender.name}:</strong> ${e.message.content}`;
                messages.appendChild(div);
                messages.scrollTop = messages.scrollHeight;
            });

        form.addEventListener('submit', async (ev) => {
            ev.preventDefault();
            const contentInput = form.querySelector('input[name="content"]');
            await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ content: contentInput.value })
            });
            contentInput.value = '';
        });
    }
});
