(function(birdmin){

    /**
     * URL class.
     * @param url string
     * @constructor
     */
    function URL(url)
    {
        // Instance.
        var self = this;

        this.parameters = {};
        this.pathname = null;
        this.href = null;
        this.hostname = null;
        this.origin = null;
        this.port = null;
        this.protocol = null;
        this.hash = null;

        parse(url);

        this.pathname = this.pathname.trimMask('/');

        /**
         * Return the segments or segment.
         * This is a 1 based index, like Laravel.
         * @param n Number optional
         * @returns {array|string|null}
         */
        this.segment = function(n)
        {
            var parts = this.pathname.split("/");
            if (n && n>0) {
                return parts[n-1] ? parts[n-1] : null;
            }
            return parts;
        };

        /**
         * Set a query parameter.
         * @param key string|object
         * @param value string
         * @returns {birdmin.URL}
         */
        this.setParameter = function (key,value)
        {
            if (key instanceof 'object') {
                for (var prop in key) {
                    this.parameters[key] = value;
                }
            } else {
                this.parameters[key] = value;
            }
            return this;
        };

        /**
         * Check if the given segment equals the value.
         * @param n Number
         * @param value string
         * @returns {boolean}
         */
        this.isSegment = function(n, value)
        {
            return this.segment(n) == value;
        };

        /**
         * Check if the URL path contains the given string.
         * @param string
         * @returns {boolean}
         */
        this.has = function(string)
        {
            return this.getUrl().search(string) > -1;
        };

        /**
         * Check if the given relative url string segments are present in this url.
         * @param string string - like /cms/pages/edit
         * @returns {boolean}
         */
        this.hasSegments = function(string)
        {
            var segments = string.trimMask("/").split("/");
            var hasThem = true;
            for (var i=0; i<segments.length; i++) {
                if (segments[i] != this.segment(i+1)) {
                    hasThem = false;
                    break;
                }
            }
            return hasThem;
        };

        /**
         * Return the base URL string of the application.
         * @returns {string}
         */
        this.getBaseUrl = function()
        {
            return [this.protocol+"/", this.host].join("/") + "/";
        };

        /**
         * Return the complete url.
         * @returns {*}
         */
        this.getUrl = function()
        {
            return this.getBaseUrl()+this.pathname+($.param(this.parameters))+(this.hash || "");
        };

        /**
         * Return the relative url path.
         * @returns {string}
         */
        this.getRelativeUrl = function()
        {
            return "/"+this.pathname+($.param(this.parameters))+(this.hash || "");
        };

        /**
         * Check if the given path is different from this url (does not look at params or hash)
         * @param path string
         * @returns {boolean}
         */
        this.isDifferent = function(path)
        {
            var url = new URL(path);
            return this.pathname !== url.pathname;
        };

        /**
         * Parse the incoming url.
         * @param url
         */
        function parse(url)
        {
            // Hack parses URL string into components.
            var loc = document.createElement("a");
            loc.href = url;

            // Generate the url parts.
            var parts = ['pathname','href','hostname','host','origin','port','protocol','hash'];
            parts.forEach(function(part) {
                self[part] = loc[part] || null;
            });

            // Parse query string.
            if (loc.search!="") {
                var search = loc.search.slice(1,-1);
                var pairs = search.split("&");
                for (var i=0; i<pairs.length; i++) {
                    var keyval = pairs[i].split("=");
                    self.parameters[keyval[0]] = keyval[1];
                }
            }
        }

    }

    /**
     * Return a string returns the URL path.
     * @returns {*}
     */
    URL.prototype.toString = function() {
        return this.getUrl();
    };


    birdmin.URL = URL;

})(birdmin);