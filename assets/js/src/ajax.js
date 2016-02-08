(function(birdmin){

    var app = birdmin.app;

    app.factory('ajax', ['$http','state', AjaxFactory]);

    function AjaxFactory($http,state)
    {
        var appHistory = birdmin.history;
        /**
         * Puts together a request that goes to Laravel.
         * Laravel marks requests as Ajax when certain headers are given.
         * @param params object
         * @param data object
         * @returns object
         */
        function request (params,data)
        {
            return {
                params: params, data: data, headers: {"X-Requested-With": "XMLHttpRequest"}
            }
        }

        /**
         * The Ajax Helper class.
         * @constructor
         */
        function Ajax()
        {
            var self = this;

            this.state = state;

            /**
             * Send an http request.
             * @param url string
             * @param params object, optional
             * @returns {HttpPromise}
             */
            this.get = function(url,params)
            {
                return $http.get(url, request(params));
            };


            /**
             * Send a POST request.
             * @param url string
             * @param params object
             * @param data object
             * @returns {HttpPromise}
             */
            this.post = function(url,params,data)
            {
                return $http.post(url, data, request(params));
            };


            /**
             * Generic error handler.
             * @param response
             */
            this.error = function(response)
            {
                console.log(response);
            };

            /**
             * Successful POST requests should notify user of what happened.
             * The server needs to generate the proper JSON response.
             */
            this.notify = function()
            {
                state.loading = true;

                return function (response) {
                    state.notify(response.data);
                    state.loading = false;
                    // Push over to the redirected page if given.
                    // This usually occurs after creating the object.
                    if (response.data.redirect) {
                        self.get(response.data.redirect).then(self.link(true), self.error);
                    }
                };

            };

            /**
             * Fired whenever a link is clicked and a success response occurs.
             * This is fired inside the HTTP Promise.
             * @param push boolean - push to history?
             * @returns {Function}
             */
            this.link = function(push)
            {
                state.processing = true;

                return function(response) {
                    state.setResponse(response);
                    if (push) {
                        appHistory.push(state.url);
                    }
                    state.processing = false;
                    birdmin.ui.register();
                }
            }
        }


        return new Ajax();
    }



})(birdmin);