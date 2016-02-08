(function(app){

    /**
     * The table controller.
     * Works with ui/table.js
     */
    function TableController($table,$scope,$attrs,state)
    {
        $scope.data = {};
        $scope.search = null;
        $scope.attrs = $attrs;
        $scope.state = state;
        $scope.data = state.table;

        $scope.doSort = function(column,header) {
            table.sort(column,header);
        };
        $scope.doSearch = function() {
            table.search($scope.search);
        };


        $("#searchTable").on('change',$scope.doSearch);

        var table = new $table($scope);
    }

    app.controller('TableController', [
        '$table',
        '$scope',
        '$attrs',
        'state',
        TableController
    ]);

})(birdmin.app);