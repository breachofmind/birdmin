(function(birdmin){

    var app = birdmin.app;
    var config = birdmin.config;

    function TableFactory(ajax,state)
    {
        /**
         * Birdmin Table class.
         * Facilitates backend interaction, data exchanges, sort, search and filter.
         * @param $scope
         * @param $http
         * @param $attrs
         * @constructor
         */
        return function Table ($scope)
        {
            ajax.table = $scope;

            var self = this;
            var $attrs = $scope.attrs;
            var $viewport = $("#Viewport");

            // The table element.
            this.$el = $("#"+$attrs.id);

            // URL endpoint the retreive table data.
            this.endpoint = $attrs.endpoint;


            /**
             * Events for this object.
             * @type {{SCROLL: Function}}
             */
            var Event = {
                /**
                 * Fires when the page scrolls.
                 * @param event
                 * @constructor
                 */
                SCROLL: function(event) {
                    if (self.canPaginate()) {
                        self.paginate();
                    }
                }
            };


            /**
             * Get/Set the processing state (for refreshes).
             * @param bool
             * @returns {*}
             */
            this.processing = function(bool)
            {
                state.loading = bool;
                return this.$el.processing(bool);
            };

            /**
             * Get/Set the loading state (for pagination).
             * @param bool
             * @returns {*}
             */
            this.loading = function(bool)
            {
                return this.$el.loading(bool);
            };


            /**
             * Send a request for data to Laravel.
             * @param params object
             */
            this.send = function (params)
            {
                if (this.processing()) {
                    return;
                }
                this.processing(true);
                ajax.get(self.endpoint, params).then(self.refresh, ajax.error);
            };


            /**
             * Perform a sort request, given the field name and clicked header object.
             * @param column string
             * @param header object
             */
            this.sort = function (column,header)
            {
                if (header.orderby == false) {
                    return;
                }
                header.orderby = (header.orderby=="asc" ? "desc" : "asc");
                var params = {
                    orderby: column,
                    dir:     header.orderby,
                    s:       $scope.search
                };
                this.send(params);
            };


            /**
             * Perform a search request, given the search value.
             * @param value string
             */
            this.search = function(value)
            {
                this.send({"s":value});
            };

            /**
             * Performs a pagination requests by incrementing the current page.
             * Data is appended to the table.
             * @return void
             */
            this.paginate = function ()
            {
                self.loading(true);
                var params = {
                    orderby: $scope.data.orderby,
                    dir:     $scope.data.dir,
                    p:       $scope.data.currentPage + 1
                };
                ajax.get(self.endpoint, params).then(self.append, ajax.error);
            };


            /**
             * Refreshes the table/scope data with the response data.
             * See TableController.
             * @param response
             */
            this.refresh = function(response)
            {
                $scope.data = response.data.table;
                self.processing(false);
            };


            /**
             * Appends the response data to the table, instead of refreshing it.
             * Used for pagination requests.
             * @param response
             */
            this.append = function(response)
            {
                var previous = $scope.data.rows;
                $scope.data = response.data.table;
                $scope.data.rows = previous.concat($scope.data.rows);
                self.loading(false);
            };



            /**
             * Determine if the user is current scrolled toward the bottom of the table.
             * Used for pagination requests.
             * @param offset int - offset below the bottom of the page to fire pagination
             * @returns {boolean}
             */
            this.atBottom = function (offset)
            {
                var tableBottom = this.$el.offset().top + this.$el.height();
                return ($viewport.scrollTop() + window.innerHeight) > tableBottom - offset;
            };


            /**
             * Determine if the table can perform a pagination request.
             * @returns {boolean}
             */
            this.canPaginate = function ()
            {
                var data = $scope.data;
                if (this.loading() || this.processing()) return false;
                if (!this.atBottom(config.tableOffset)) return false;
                if (data.currentPage >= data.lastPage) return false;

                return true;
            };


            // Binds the scroll event to the table paginator.
            $viewport.off('scroll');
            $viewport.on('scroll', Event.SCROLL);
        }
    }

    app.factory('$table', ['ajax','state', TableFactory]);

})(birdmin);