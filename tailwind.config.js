module.exports = {
    prefix: 'nw-',
    purge: {
        enabled: true,
        content: [
            './resources/views/**/*.blade.php',
            './src/helpers.php',
        ],
    },
    safelist: [
        'focus:nw-outline-none',
        'nw-underline',
    ],
    theme: {
        extend: {},
    },
    plugins: []
}
