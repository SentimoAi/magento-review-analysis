var config = {
    paths: {
        'sentimoWidget': 'https://sentimoai.com/widget/review-helper-1.0.0.min'
    },
    shim: {
        'sentimoWidget': {
            deps: ['jquery'],
            exports: 'Sentimo'
        }
    }
};
