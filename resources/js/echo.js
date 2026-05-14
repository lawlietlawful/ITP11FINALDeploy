import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.realtimeConnection = {
    connected: false,
    state: 'initialized',
    updatedAt: new Date().toISOString(),
    lastError: null,
};

const updateRealtimeConnection = (state, details = {}) => {
    window.realtimeConnection = {
        ...window.realtimeConnection,
        ...details,
        state,
        connected: state === 'connected',
        updatedAt: new Date().toISOString(),
    };

    window.dispatchEvent(new CustomEvent('realtime-connection-changed', {
        detail: window.realtimeConnection,
    }));
};

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

const connection = window.Echo?.connector?.pusher?.connection;

if (connection) {
    connection.bind('connected', () => {
        updateRealtimeConnection('connected', { lastError: null });
    });

    connection.bind('disconnected', () => {
        updateRealtimeConnection('disconnected');
    });

    connection.bind('unavailable', () => {
        updateRealtimeConnection('unavailable');
    });

    connection.bind('error', (error) => {
        updateRealtimeConnection('error', { lastError: error });
    });

    connection.bind('state_change', (states) => {
        updateRealtimeConnection(states.current);
    });
}
