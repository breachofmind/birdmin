(function(app){

    /**
     * Internal application link directive brd-link.
     * <a href="/cms/url" brd-link>Label</a>
     */
    function brdLink (ajax)
    {

        return {
            restrict:"A",
            link: function(scope,element,attrs)
            {
                element.on('click', function(event) {
                    event.preventDefault();
                    if (! ajax.state.url.isDifferent(attrs.href)) return;
                    ajax.get(attrs.href).then(ajax.link(true), ajax.error);
                });
            }
        }
    }

    function brdTab (state)
    {
        return {
            restrict:"A",
            link: function(scope,element,attrs)
            {
                var hash = attrs.brdTab;
                element.on('click', function(event) {
                    state.url = new birdmin.URL(attrs.href);
                    scope.$apply();
                    console.log(scope);
                });
            }
        }
    }

    /**
     * Handles a form submission.
     * Scope should be the FormController scope.
     */
    function brdSubmit (ajax,state)
    {
        return {
            restrict:"A",
            link: function(scope,element,attrs)
            {
                element.on('click', function(event) {
                    event.preventDefault();
                    var form = $("#"+attrs.brdSubmit);
                    if (form.length) {
                        var data = form.serializeObject();
                        return ajax.post(form.attr('action'), null, data).then(ajax.notify(), ajax.error);
                    }
                });
            }
        }
    }

    /**
     * Allows printing of HTML content into elements.
     * <div bind-unsafe-html="view"></div>
     */
    function bindUnsafeHtml ($compile)
    {
        return function(scope,element,attrs) {
            scope.$watch(
                function(scope) {
                    return scope.$eval(attrs.bindUnsafeHtml);
                },
                function(value) {
                    element.html(value);
                    $compile(element.contents())(scope);
                }
            );
        };
    }

    /**
     * Adds attributes dynamically to an element.
     * @returns {Function}
     */
    function addAttributes ()
    {
        return {
            restrict:"A",
            scope:{
                ngAttrs: '='
            },
            link:function(scope,element,attrs) {
                var attributes = scope.ngAttrs;
                for (var attr in attributes)
                {
                    var value = attributes[attr];
                    if (attr=="class") {
                        element.addClass(value);
                        continue;
                    }
                    element.attr(attr,value);
                }
                element.removeAttr('ng-attrs');

            }
        }
    }

    // Create the directives.
    app.directive('brdLink', ['ajax', brdLink]);
    app.directive('brdTab', ['state', brdTab]);
    app.directive('brdSubmit', ['ajax', 'state', brdSubmit]);
    app.directive('bindUnsafeHtml', ['$compile', bindUnsafeHtml]);
    app.directive('ngAttrs', addAttributes);

})(birdmin.app);