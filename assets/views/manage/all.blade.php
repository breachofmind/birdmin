<section id="{{ $class::plural()."Table" }}" class="brd-table" ng-controller="TableController as table" data-endpoint="{{ cms_url($class::plural()) }}">

    <header class="brd-table-header">
        <div class="brd-table-row brd-table-meta">
            <div><input id="searchTable" type="search" placeholder="Search..." ng-model="search"></div>
            <div>Total Records: <strong>@{{data.total}}</strong></div>
        </div>
        <div class="brd-table-row brd-table-headers">
            <div ng-repeat="(field,header) in data.headers"
                 data-column="@{{ field }}"
                 ng-click="doSort(field, header)"
                 ng-class="{sort:header.orderby, asc:header.orderby=='asc', desc:header.orderby=='desc'}">
                @{{ header.label }}
            </div>
        </div>
    </header>

    <div class="brd-table-body">
        <div class="brd-table-row" ng-repeat="(i,model) in data.rows">
            <div ng-repeat="(field,cell) in model" bind-unsafe-html="cell" data-column="@{{ field }}"></div>
        </div>

        <div class="brd-table-row" ng-show="!data.rows.length">
            <div>
                <div class="alert-box warning"><p>No {{$class::plural()}} found.</p></div>
            </div>
        </div>
    </div>

    <footer class="brd-table-footer">
        <div class="overlay"></div>
    </footer>

</section>