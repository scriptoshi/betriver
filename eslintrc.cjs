module.exports = {
    env: {
        browser: true,
        es2021: true,
        node: true
    },

    extends: [
        'plugin:vue/vue3-essential',
        'standard',
        '@vue/standard',
        'prettier'
    ],

    parserOptions: {
        ecmaVersion: 2020,
        sourceType: 'module'
    },

    plugins: ['vue'],

    rules: {
        'vue/multi-word-component-names': 0,
        'no-return-assign': 'off',
        'no-unused-vars': 2,
        "semi": [2, "always"],
        'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
        'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
        "import/order": [
            "error",
            {
                "groups": ["builtin", "external", "internal"],
                "pathGroups": [
                    {
                        "pattern": "vue",
                        "group": "external",
                        "position": "before"
                    },
                    {
                        "pattern": "./web3js",
                        "group": "internal",
                        "position": "before"
                    },

                ],

                "pathGroupsExcludedImportTypes": ["vue", "~/web3js"],
                "newlines-between": "always",
                "alphabetize": {
                    "order": "asc",
                    "caseInsensitive": true
                }
            }
        ],
    },
    root: true,

};
