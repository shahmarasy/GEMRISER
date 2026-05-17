import js from '@eslint/js';

export default [
    js.configs.recommended,
    {
        languageOptions: {
            globals: {
                Alpine: 'readonly',
                axios: 'readonly',
            },
        },
        rules: {
            'no-unused-vars': 'warn',
            'no-console': 'warn',
        },
    },
];
