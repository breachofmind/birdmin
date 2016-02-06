<h1 class="brd-title">{{$class::singular()}} Tree</h1>


<section id="{{ $class::plural()."Tree" }}">
<ul>
    <li ng-repeat="object in state.data.roots">
        @{{ object.title }}
        <tree model="object"></tree>

    </li>
</ul>


</section>

<script type="text/x-handlebars-template" id="treeTemplate">
    <ul ng-if="model.children.length">
        <li ng-repeat="object in model.children">
            @{{ object.title }}
            <tree model="object"></tree>
        </li>
    </ul>
</script>