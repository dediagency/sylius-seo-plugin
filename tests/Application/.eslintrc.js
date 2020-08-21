module.exports = {
    extends: 'airbnb-base',
    env: {
        node: true,
    },
    rules: {
        'indent': ["error", 4],
        'object-shorthand': ['error', 'always', {
            avoidQuotes: true,
            avoidExplicitReturnArrows: true,
        }],
        'function-paren-newline': ['error', 'consistent'],
        'max-len': ['warn', 120, 2, {
            ignoreUrls: true,
            ignoreComments: false,
            ignoreRegExpLiterals: true,
            ignoreStrings: true,
            ignoreTemplateLiterals: true,
        }],
    },
};
