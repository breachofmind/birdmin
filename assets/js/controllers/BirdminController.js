(function(birdmin){

    var app = birdmin.app;

    /**
     * The main birdmin application controller.
     */
    function BirdminController($scope, state, ajax)
    {
        var appHistory = birdmin.history;

        $scope.template = {
            title: 'Birdmin v1.0',
            body: '',
        };

        $scope.state = state;

        /**
         * For creating a history for AJAX page loads.
         * Note - chrome will fire this immediately when the page loads.
         */
        $(window).bind ('popstate', function() {
            if (appHistory.popped) {
                appHistory.pull(ajax);
            }
        });

        // Initial load should grab view of url.
        ajax.get(window.location.pathname).then(ajax.link(), ajax.error);
    }

    app.controller('BirdminController', [
        '$scope',
        'state',
        'ajax',
        BirdminController
    ]);

})(birdmin);