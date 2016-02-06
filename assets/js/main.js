(function(birdmin){

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

        return new function ApplicationState() {

            this.view = false;
            this.views = "";
            this.data = {};
            this.actions = "";
            this.processing = false;
            this.loading = false;
            this.collapse = false;
            this.response = {};

            this.notifications = {
                messageBag: [],
                errorBag: []
            };

            this.clearDeck = function()
            {
                this.actions = [];
                this.views = [];
            };

            this.setResponse = function(response)
            {
                this.url = new URL(response.config.url);
                this.view = response.data.view;
                this.response = response;
                this.data = response.data;
                this.user = response.data.user;
                this.actions = response.data.actions || [];
                this.views = response.data.views || "";
            };

            this.notify = function (response)
            {
                this.notifications = response;

                $timeout(function(){
                    this.clearMessages();
                }.bind(this), config.alertTimeout);
            };

            this.hasMessages = function()
            {
                return (this.notifications.messageBag.length > 0
                || this.notifications.errorBag.length > 0);
            };

            this.clearMessages = function()
            {
                log.push($.extend(true,{},this.notifications));
                this.notifications.messageBag = [];
                this.notifications.errorBag = [];
            };

        };
    }

    app.service('state', ['$timeout', ApplicationStateService]);

    birdmin.app = app;
    window.birdmin = birdmin;

})( {} );