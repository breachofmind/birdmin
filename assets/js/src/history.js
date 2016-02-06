(function(birdmin){

    function HistoryController()
    {
        var page = 0;
        var state = function(n) {
            return {page:n};
        };

        this.popped = false;
        /**
         * Push a URL into the history.
         * @param url string
         * @returns {Object}
         */
        this.push = function(url)
        {
            if (url !== location.pathname+(location.query ? "" : "?"+location.query)) {
                this.popped = true;
            }
            page++;
            history.pushState(state(page), url, url);
            return history.state;
        };


        this.pull = function(ajax)
        {
            ajax.get(location.href).then(ajax.link(false), ajax.error);
        };
    }

    birdmin.history = new HistoryController();

})(birdmin);