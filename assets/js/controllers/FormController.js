(function(app){

    function FormController($scope,$attrs,state,Slug)
    {
        var $form = $attrs.$$element;

        $scope.titleField = null;
        $scope.slugField = null;

        $scope.slugify = function() {
            $scope.slugField = Slug.slugify($scope.titleField);
        }
    }

    app.controller('FormController', [
        '$scope',
        '$attrs',
        'state',
        'Slug',
        FormController
    ]);

})(birdmin.app);