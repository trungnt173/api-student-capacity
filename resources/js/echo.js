import io from 'socket.io-client';
import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.host,
    client: io,
});
