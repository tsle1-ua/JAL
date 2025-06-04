import 'bootstrap';

function initAutocomplete(inputId, endpoint, listId) {
    const input = document.getElementById(inputId);
    const list = document.getElementById(listId);
    if (!input || !list) return;

    input.addEventListener('input', () => {
        const query = input.value.trim();
        if (query.length < 2) {
            list.innerHTML = '';
            list.classList.add('d-none');
            return;
        }

        fetch(`${endpoint}?q=${encodeURIComponent(query)}`)
            .then(r => r.json())
            .then(data => {
                const results = data.listings || data.events || data.results || [];
                list.innerHTML = '';
                results.forEach(item => {
                    const a = document.createElement('a');
                    a.href = item.url;
                    a.textContent = item.title;
                    a.className = 'list-group-item list-group-item-action';
                    list.appendChild(a);
                });
                list.classList.toggle('d-none', results.length === 0);
            })
            .catch(() => {
                list.innerHTML = '';
                list.classList.add('d-none');
            });
    });

    document.addEventListener('click', (e) => {
        if (e.target !== input && !list.contains(e.target)) {
            list.innerHTML = '';
            list.classList.add('d-none');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initAutocomplete('listing-search', '/api/listings/suggest', 'listing-suggestions');
    initAutocomplete('event-search', '/api/events/suggest', 'event-suggestions');
});
