import 'bootstrap';
import axios from 'axios';
import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.min.css';

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

    const priceSlider = document.getElementById('price-slider');
    const priceInput = document.getElementById('price_range');
    if (priceSlider && priceInput) {
        const startValues = priceInput.value.split(',').map(v => parseInt(v, 10));
        noUiSlider.create(priceSlider, {
            start: startValues,
            connect: true,
            range: {
                min: 0,
                max: 2000
            },
            tooltips: [true, true],
            format: {
                to: value => Math.round(value),
                from: value => Number(value)
            }
        });

        const minEl = document.getElementById('price-slider-min');
        const maxEl = document.getElementById('price-slider-max');
        priceSlider.noUiSlider.on('update', (values) => {
            priceInput.value = values.map(v => Math.round(v)).join(',');
            if (minEl) minEl.textContent = Math.round(values[0]);
            if (maxEl) maxEl.textContent = Math.round(values[1]);
        });
    }

    const cityInput = document.querySelector('input[name="city"]');
    const cityList = document.getElementById('city-suggestions');
    if (cityInput && cityList) {
        let cancelToken;
        cityInput.addEventListener('input', () => {
            const term = cityInput.value.trim();
            if (term.length < 2) {
                cityList.innerHTML = '';
                return;
            }
            if (cancelToken) cancelToken.cancel();
            cancelToken = axios.CancelToken.source();
            axios.get('/api/cities', {
                params: { term },
                cancelToken: cancelToken.token,
                headers
            }).then(response => {
                cityList.innerHTML = '';
                response.data.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    cityList.appendChild(option);
                });
            }).catch(error => {
                if (!axios.isCancel(error)) console.error(error);
            });
        });
    }

    const container = document.getElementById('listing-container');
    const pagination = document.getElementById('pagination-links');
    const sentinel = document.getElementById('load-more-sentinel');
    if (container && pagination && sentinel) {
        const observer = new IntersectionObserver(entries => {
            if (entries.some(e => e.isIntersecting)) {
                loadNextPage();
            }
        }, { rootMargin: '100px' });

        observer.observe(sentinel);

        async function loadNextPage() {
            const nextLink = pagination.querySelector('a[rel="next"]');
            if (!nextLink) {
                observer.disconnect();
                return;
            }

            const url = new URL(nextLink.href);
            url.pathname = '/api/listings/cards';

            try {
                const response = await axios.get(url.toString(), { headers });
                container.insertAdjacentHTML('beforeend', response.data.html);
                pagination.innerHTML = response.data.links;
            } catch (error) {
                console.error(error);
            }
        }
    }
});
