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

        $scope.doSort = function(column,header) {
            table.sort(column,header);
        };
        $scope.doSearch = function() {
            table.search($scope.search);
        };
        if (state.response.data.table) {
            $scope.data = state.response.data.table;
        }
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