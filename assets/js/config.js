(function(birdmin){

    var app = birdmin.app;

    /**
     * Birdmin configuration object.
     * @constructor
     */
    function BirdminConfig()
    {
        // Time in ms before alerts disappear.
        this.alertTimeout = 3000;

        // Offset from the bottom of table to fire pagination request.
        this.tableOffset = 200;

        // Minimum time in ms a refresh occurs.
        // This is mostly to see the cool animation.
        this.tableMinimumLoadTime = 400;
    }

    /**
     * httpProvider configuration.
     */
    function AngularConfiguration($httpProvider)
    {
        /**
         * Creates a short delay between request and response,
         * to make room for animation.
         */
        $httpProvider.interceptors.push(function($q,$timeout) {
            return {
                'request': function(config) {
                    return $timeout(function() { return config; }, 250);
                },
                'response': function(response) {
                    return response;
                }
            };
        });
    }

    app.config(['$httpProvider', AngularConfiguration]);

    birdmin.config = new BirdminConfig();

})(birdmin);