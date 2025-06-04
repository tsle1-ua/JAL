import 'bootstrap';
import axios from 'axios';
import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.min.css';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    forceTLS: true,
});

document.addEventListener('DOMContentLoaded', () => {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
            .catch(err => console.error('SW registration failed:', err));
    }
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const headers = {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    };

    const htmlEl = document.documentElement;
    const themeToggle = document.getElementById('theme-toggle');
    const toggleIcon = themeToggle?.querySelector('i');

    function setIcon(theme) {
        if (!toggleIcon) return;
        toggleIcon.classList.toggle('bi-moon-fill', theme === 'light');
        toggleIcon.classList.toggle('bi-sun-fill', theme === 'dark');
    }

    const storedTheme = localStorage.getItem('theme') || 'light';
    htmlEl.setAttribute('data-bs-theme', storedTheme);
    setIcon(storedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const newTheme = htmlEl.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            htmlEl.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            setIcon(newTheme);
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
    const skeletonTemplate = document.getElementById('skeleton-card-template');

    function showSkeletons(count = 3) {
        const skeletons = [];
        if (!skeletonTemplate) return skeletons;
        for (let i = 0; i < count; i++) {
            const clone = skeletonTemplate.content.firstElementChild.cloneNode(true);
            container.appendChild(clone);
            skeletons.push(clone);
        }
        return skeletons;
    }

    function removeSkeletons(nodes) {
        nodes.forEach(n => n.remove());
    }
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

            const skeletons = showSkeletons();
            try {
                const response = await axios.get(url.toString(), { headers });
                removeSkeletons(skeletons);
                container.insertAdjacentHTML('beforeend', response.data.html);
                pagination.innerHTML = response.data.links;
            } catch (error) {
                removeSkeletons(skeletons);
                console.error(error);
            }
        }
    }

    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        const steps = registerForm.querySelectorAll('.form-step');
        const progressBar = document.getElementById('register-progress');
        let currentStep = parseInt(registerForm.dataset.step || '0', 10);

        function updateProgress() {
            if (!progressBar) return;
            const percent = ((currentStep + 1) / steps.length) * 100;
            progressBar.style.width = `${percent}%`;
        }

        function showStep(index) {
            steps.forEach((step, i) => {
                step.classList.toggle('d-none', i !== index);
            });
            updateProgress();
        }

        registerForm.querySelectorAll('[data-next]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const inputs = steps[currentStep].querySelectorAll('input, select');
                for (const input of inputs) {
                    if (!input.reportValidity()) {
                        return;
                    }
                }
                if (currentStep < steps.length - 1) {
                    currentStep++;
                    showStep(currentStep);
                }
            });
        });

        registerForm.querySelectorAll('[data-prev]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
        });

        showStep(currentStep);
    }

    const calendarEl = document.getElementById('calendar');
    const listEl = document.getElementById('event-list');
    const toggleBtn = document.getElementById('toggle-view');
    const mapEl = document.getElementById('event-map');

    if (calendarEl) {
        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            events: JSON.parse(calendarEl.dataset.events)
        });
        calendar.render();

        if (toggleBtn && listEl) {
            toggleBtn.addEventListener('click', () => {
                listEl.classList.toggle('d-none');
                calendarEl.classList.toggle('d-none');
                toggleBtn.textContent = listEl.classList.contains('d-none') ? 'Ver lista' : 'Ver calendario';
            });
        }
    }

    if (mapEl) {
        window.initEventMap = function () {
            const center = JSON.parse(mapEl.dataset.center);
            const zoom = parseInt(mapEl.dataset.zoom, 10);
            const map = new google.maps.Map(mapEl, { center, zoom });
            const events = JSON.parse(mapEl.dataset.events);

            events.forEach(evt => {
                if (!evt.lat || !evt.lng) return;
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(evt.lat), lng: parseFloat(evt.lng) },
                    map,
                    title: evt.title
                });
                marker.addListener('click', () => {
                    window.location.href = evt.url;
                });
            });
        };


        if (window.google && window.google.maps) {
            window.initEventMap();
        }
    }

    const listingMapEl = document.getElementById('listing-map');
    if (listingMapEl) {
        window.initListingMap = function () {
            const center = JSON.parse(listingMapEl.dataset.center);
            const zoom = parseInt(listingMapEl.dataset.zoom, 10);
            const latInput = document.querySelector('input[name="latitude"]');
            const lngInput = document.querySelector('input[name="longitude"]');
            const startLat = parseFloat(latInput.value) || center.lat;
            const startLng = parseFloat(lngInput.value) || center.lng;
            const map = new google.maps.Map(listingMapEl, {
                center: { lat: startLat, lng: startLng },
                zoom
            });

            let marker;

            function setMarker(latLng) {
                if (!marker) {
                    marker = new google.maps.Marker({ position: latLng, map });
                } else {
                    marker.setPosition(latLng);
                }
            }

            map.addListener('click', e => {
                setMarker(e.latLng);
                if (latInput) latInput.value = e.latLng.lat();
                if (lngInput) lngInput.value = e.latLng.lng();
            });

            if (latInput.value && lngInput.value) {
                const pos = new google.maps.LatLng(startLat, startLng);
                setMarker(pos);
            }
        };

        if (window.google && window.google.maps) {
            window.initListingMap();
        }
    }

    const messagesEl = document.getElementById('messages');
    const msgForm = document.getElementById('message-form');
    if (messagesEl && msgForm) {
        const input = msgForm.querySelector('input[name="content"]');
        const matchId = messagesEl.dataset.matchId;

        function appendMessage(message) {
            const div = document.createElement('div');
            div.classList.add('mb-2');
            div.innerHTML = `<strong>${message.sender.name}:</strong> ${message.content}`;
            messagesEl.appendChild(div);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        msgForm.addEventListener('submit', e => {
            e.preventDefault();
            const content = input.value.trim();
            if (!content) return;
            axios.post(msgForm.action, { content }, { headers })
                .then(res => {
                    appendMessage(res.data);
                    input.value = '';
                })
                .catch(err => console.error(err));
        });

        Echo.private(`match.${matchId}`)
            .listen('MessageSent', e => {
                appendMessage(e.message);
            });
    }
});
