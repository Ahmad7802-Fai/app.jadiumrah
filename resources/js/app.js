import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {

    const buttons = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');

    buttons.forEach(button => {
        button.addEventListener('click', function () {

            const tab = this.dataset.tab;

            contents.forEach(c => c.classList.add('hidden'));
            document.getElementById(tab).classList.remove('hidden');

            buttons.forEach(b => b.classList.remove('tab-active'));
            this.classList.add('tab-active');
        });
    });

});