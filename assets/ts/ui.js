(function (birdmin) {
    var app = birdmin.app;
    var button = "\n    <a ng-attrs=\"button.attributes\">\n        <i ng-if=\"button.icon\" class=\"lnr-{{button.icon}}\"></i>\n        <span>{{button.label}}</span>\n    </a>\n    ";
    var group = "<button-action ng-repeat=\"button in buttons\" params=\"button\"/>";
    function buttonActionGroup() {
        return {
            template: group,
            restrict: "E",
            scope: { buttons: "=" },
            link: function (scope, element, attrs) {
                scope.$watch(scope.buttons, function (value) {
                    scope.buttons = value;
                });
            }
        };
    }
    function buttonAction(ajax, $compile) {
        return {
            template: button,
            restrict: "E",
            scope: { button: "=params" },
            link: function (scope, element, attrs) {
                // ngAttrs will inject some new attributes, so recompile.
                $compile(element.contents())(scope);
            }
        };
    }
    app.directive('buttonActionGroup', buttonActionGroup);
    app.directive('buttonAction', ['ajax', '$compile', buttonAction]);
})(birdmin);
//# sourceMappingURL=ui.js.map