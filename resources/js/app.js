import './bootstrap';

import Alpine from 'alpinejs';
import { initChatListener } from './chat';

window.Alpine = Alpine;

Alpine.start();

initChatListener();