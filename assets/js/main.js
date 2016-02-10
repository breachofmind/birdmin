(function(birdmin){

    var onDomReady = [];

    var services = [
        'ngSanitize',
        'ngAnimate',
        'slugifier'
    ];

    /**
     * Register then main birdmin angular application.
     * @type {module|*}
     */
    var app = angular.module('birdmin', services);

    /**
     * The main application state and data exchange.
     * Used to keep track of the url, view, processing state, etc.
     */
    function ApplicationStateService($timeout)
    {
        var config = birdmin.config;
        var URL = birdmin.URL;
        var log = [];

        function ApplicationState()
        {
            this.url = new URL(window.location.href);
            this.notifications = new Notifier();

            var defaults = {
                view:     null,
                class:    null,
                model:    null,
                data:     {},
                actions:  [],
                views:    [],
                table:    null,
                user:     null
            };

            // Toggles
            this.processing = false;
            this.loading    = false;
            this.collapse   = false;


            /**
             * Updates the application state with the response from the server.
             * @param response
             */
            this.setResponse = function(response)
            {
                this.url = new URL(response.config.url);

                for (var key in defaults) {
                    this[key] = response.data[key] || defaults[key] || null;
                }
            };

            /**
             * Notify the user with some messages.
             * @param response
             */
            this.notify = function (response)
            {
                this.notifications.set(response);

                $timeout(function(){
                    this.notifications.set();
                }.bind(this), config.alertTimeout);
            };

            /**
             * Check if the URL hash equals the given name.
             * @param name
             * @returns {boolean}
             */
            this.hash = function(name)
            {
                return this.url.hash==="#"+name;
            };
        }

        function Notifier()
        {
            var defaults = {
                errors:[],
                messages:[],
                success:true
            };

            this.set = function(response)
            {
                if (! arguments.length) {
                    response = defaults;
                }
                for (var key in response) {
                    this[key] = response[key] || defaults[key];
                }
            };

            this.log = function()
            {
                for (var key in defaults) {
                    log.push(defaults[key]);
                }
            };

            this.empty = function()
            {
                return this.errors.length == 0 && this.messages.length == 0;
            };

            this.set();
        }

        return new ApplicationState();
    }

    /**
     * Runs all the init callbacks.
     */
    function runInits()
    {
        onDomReady.forEach(function(callback) {
            callback();
        })
    }

    app.service('state', ['$timeout', ApplicationStateService]);

    /**
     * Register a callback to fire when the DOM loads.
     * @param callback
     */
    birdmin.init = function(callback)
    {
        onDomReady.push(callback);
    };

    $(document).ready(runInits);

    birdmin.app = app;
    window.birdmin = birdmin;

})( {} );