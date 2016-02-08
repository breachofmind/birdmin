(function(birdmin){
    var app = birdmin.app;

    var button:string = `
    <a ng-attrs="button.attributes">
        <i ng-if="button.icon" class="lnr-{{button.icon}}"></i>
        <span>{{button.label}}</span>
    </a>
    `;

    var group:string = `<b-action ng-repeat="button in buttons" params="button"/>`;

    function buttonActionGroup()
    {
        return {
            template: group,
            restrict: "E",
            scope: {buttons:"="},
            link: function(scope,element,attrs)
            {
                scope.$watch(scope.buttons, function(value){
                    scope.buttons = value;
                });
            }
        }
    }

    function buttonAction(ajax,$compile)
    {
        return {
            template: button,
            restrict: "E",
            scope: {button:"=params"},
            link: function(scope,element,attrs)
            {
                // ngAttrs will inject some new attributes, so recompile.
                $compile(element.contents())(scope);
            }
        }
    }

    app.directive('bGroup', buttonActionGroup);
    app.directive('bAction', ['ajax','$compile', buttonAction]);

})(birdmin);