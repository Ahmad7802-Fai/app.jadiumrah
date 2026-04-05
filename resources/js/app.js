import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;

/* ===============================
🔥 CROPPER (WAJIB)
=============================== */
import Cropper from 'cropperjs'
import 'cropperjs/dist/cropper.min.css'
import imageCompression from 'browser-image-compression'
window.imageCompression = imageCompression
window.Cropper = Cropper
/* =============================== */

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