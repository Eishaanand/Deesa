import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const storageKey = 'deesa-theme';
const root = document.documentElement;

const applyTheme = (theme) => {
    root.classList.toggle('dark', theme === 'dark');
    window.localStorage.setItem(storageKey, theme);
};

const preferredTheme = window.localStorage.getItem(storageKey)
    ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

applyTheme(preferredTheme);

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            applyTheme(root.classList.contains('dark') ? 'light' : 'dark');
        });
    });
});
