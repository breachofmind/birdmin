<div id="Notifications" role="alert" ng-class="{active:!state.notifications.empty()}">
    <div class="alert-box success" ng-repeat="message in state.notifications.messages">
        <p><i class="lnr-checkmark-circle"></i> @{{message}}</p>
    </div>
    <div class="alert-box alert" ng-repeat="message in state.notifications.errors">
        <p><i class="lnr-cross-circle"></i> @{{message}}</p>
    </div>
</div>