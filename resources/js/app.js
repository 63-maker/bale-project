import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;
window.initLucide = () => {
    createIcons({
        icons,
        attrs: {
            'stroke-width': '1.75',
            'class': 'lucide inline-block shrink-0',
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    window.initLucide();
});

// Re-init lucide after Alpine changes
document.addEventListener('alpine:initialized', () => {
    window.initLucide();
});

Alpine.start();
