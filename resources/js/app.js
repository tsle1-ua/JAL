import 'bootstrap';
import axios from 'axios';

/**
 * Handle like/dislike actions without page reload.
 */
document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf.getAttribute('content');
    }

    document.body.addEventListener('submit', async (e) => {
        const target = e.target;
        if (!(target instanceof HTMLFormElement)) {
            return;
        }

        const action = target.getAttribute('action') || '';
        if (!/roomie-match\/(like|dislike)\//.test(action)) {
            return;
        }

        e.preventDefault();

        try {
            await axios.post(action, new FormData(target));

            const card = target.closest('.col-md-4');
            if (card) {
                card.remove();
            }
        } catch (err) {
            console.error('Action failed', err);
        }
    });
});
