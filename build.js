var $ = require('builder');

/**
 * ---------------------------------------------
 * Backend CMS build configuration.
 * ---------------------------------------------
 */
$.register('cms')
    .path('asset',  'cms/assets')
    .path('scss',   '{asset}/scss')
    .path('js',     '{asset}/js')
    .path('ts',     '{asset}/ts')
    .path('jsx',    '{asset}/jsx')
    .path('views',  '{asset}/views')
    .path('import', '{js}/lib/foundation/scss')
    .path('static', 'cms/public/static')

    .collection('css', ['cms.css'], {
        dir:"{static}"
    })

    .collection('js-lib', [
        "modernizr/modernizr.js",
        "jquery/dist/jquery.js",
        "jquery.cookie/jquery.cookie.js",
        "foundation/js/foundation.js",
        "moment/moment.js",
        "pikaday/pikaday.js",
        "dropzone/dist/dropzone.js",
        "angular/angular.js",
        "angular-sanitize/angular-sanitize.js",
        "angular-animate/angular-animate.js",
        "handlebars/handlebars.js"
    ], {
        dir:"{js}/lib",
        build:"{static}/cms.lib.js"
    })


    .collection('js-src', [
        "utils.js",
        "main.js",
        "jscolor.js",
        "config.js",
        "directives.js",
        "src/url.js",
        "src/ajax.js",
        "src/history.js",
        "src/ui.js",
        "src/table.js",
        "src/slugify.js",
        "src/tree.js",
        "src/state.js",
        "controllers/BirdminController.js",
        "controllers/TableController.js",
        "controllers/FormController.js"
    ], {
        dir:"{js}",
        build:"{static}/cms.src.js"
    })


    .collection('typescript', [
        'ui.js'
    ], {
        dir:"{ts}",
        addTo:"js-src"
    });

module.exports = $;
