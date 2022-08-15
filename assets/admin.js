/*
 * Welcome to your app's admin JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

const $ = require('jquery');
window.$ = window.jQuery = $;

// Allows to replace common links behavour with form submission, utilizing this way form method
import './common/js/method-spoofed-links';

// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// Admin assets
import './admin/admin.scss';
import './admin/admin.js';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});