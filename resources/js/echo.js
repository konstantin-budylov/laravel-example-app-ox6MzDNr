import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

window.Echo.channel('imports')
    .listen('import.started', (event) => {
        console.log(event);
    })
    .listen('import.success', (event) => {
        console.log(event);
    })
    .listen('import.failed', (event) => {
        console.log(event);
    })
    .listen('import.row.success', (event) => {
        console.log(event);
    })
    .listen('import.row.failed', (event) => {
        console.log(event);
    });
