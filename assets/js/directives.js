(function(app){

    /**
     * Internal application link directive brd-link.
     * <a href="/cms/url" brd-link>Label</a>
     */
    function brdLink (ajax)
    {
        return {
            restrict:"A",
            priority:1000,
            link: function(scope,element,attrs)
            {
                element.on('click', function(event) {
                    event.preventDefault();
                    ajax.get(attrs.href).then(ajax.link(true), ajax.error);
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
    function addAttributes ($compile,$timeout)
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
            }
        }
    }

    // Create the directives.
    app.directive('brdLink', ['ajax', brdLink]);
    app.directive('brdSubmit', ['ajax', 'state', brdSubmit]);
    app.directive('bindUnsafeHtml', ['$compile', bindUnsafeHtml]);
    app.directive('ngAttrs', ['$compile','$timeout', addAttributes]);

})(birdmin.app);