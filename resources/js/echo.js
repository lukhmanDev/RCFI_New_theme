import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const broadcastDriver = window.BROADCAST_DRIVER || (import.meta.env.VITE_BROADCAST_CONNECTION ?? 'log');
const reverbHost = import.meta.env.VITE_REVERB_HOST;
const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const isReverbHostLocal = !reverbHost || reverbHost === 'localhost' || reverbHost === '127.0.0.1';

const createDummyEcho = () => {
    const dummyChannel = {
        listen: () => dummyChannel,
        listenToAll: () => dummyChannel,
        stopListening: () => dummyChannel,
        whisper: () => dummyChannel,
        error: () => dummyChannel,
        subscribed: () => dummyChannel,
    };
    return {
        private: () => dummyChannel,
        channel: () => dummyChannel,
        encryptedPrivate: () => dummyChannel,
        join: () => dummyChannel,
        leave: () => {},
        leaveChannel: () => {},
        disconnect: () => {},
        socketId: () => undefined,
    };
};

if ((broadcastDriver === 'reverb' || broadcastDriver === 'pusher') && import.meta.env.VITE_REVERB_APP_KEY && (!isReverbHostLocal || isLocal)) {
    try {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: reverbHost || window.location.hostname,
            wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
            wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
            unavailableTimeout: 1000,
            maxReconnectionAttempts: 1,
        });
    } catch (e) {
        window.Echo = createDummyEcho();
    }
} else {
    window.Echo = createDummyEcho();
}
