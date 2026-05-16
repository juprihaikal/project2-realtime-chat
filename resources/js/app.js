import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.Echo.channel('chat')
    .listen('MessageSent', (e) => {
        console.log(e.message);
        alert(e.message);
    });