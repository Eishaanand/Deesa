import forms from '@tailwindcss/forms';

export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                ink: '#0f172a',
                mist: '#e2e8f0',
                skyglass: '#8fd3ff',
                mintglass: '#b2f5ea',
                peachglass: '#ffd7ba',
            },
            boxShadow: {
                glass: '0 20px 60px rgba(15, 23, 42, 0.12)',
            },
            backdropBlur: {
                xs: '2px',
            },
            fontFamily: {
                display: ['"Satoshi"', 'ui-sans-serif', 'system-ui'],
                body: ['"General Sans"', 'ui-sans-serif', 'system-ui'],
            },
        },
    },
    plugins: [forms],
};
