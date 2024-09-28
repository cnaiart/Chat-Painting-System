/* eslint-env node */
require('@rushstack/eslint-patch/modern-module-resolution')

module.exports = {
    root: true,
    ignorePatterns: ['src/uni_modules/', 'src/lib'],
    extends: [
        'plugin:vue/vue3-essential',
        'eslint:recommended',
        '@vue/eslint-config-typescript/recommended',
        '@vue/eslint-config-prettier'
    ],
    rules: {
        'prettier/prettier': [
            'warn',
            {
                semi: false,
                singleQuote: true,
                printWidth: 80,
                proseWrap: 'preserve',
                bracketSameLine: false,
                endOfLine: 'auto',
                tabWidth: 4,
                useTabs: false,
                trailingComma: 'none'
            }
        ],
        'no-empty': 'off',
        'vue/multi-word-component-names': 'off',
        '@typescript-eslint/no-explicit-any': 'off',
        '@typescript-eslint/ban-ts-comment': 'off',
        'no-undef': 'off',
        'vue/prefer-import-from-vue': 'off',
        'no-prototype-builtins': 'off',
        'prefer-spread': 'off',
        '@typescript-eslint/no-non-null-assertion': 'off',
        '@typescript-eslint/no-non-null-asserted-optional-chain': 'off',
        'vue/no-v-text-v-html-on-component': 'off'
    },
    globals: {}
}
