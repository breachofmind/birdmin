<div id="Notifications" role="alert" ng-class="{active:state.hasMessages()}">
    <div class="alert-box success" ng-repeat="message in state.notifications.messageBag">
        <p><i class="lnr-checkmark-circle"></i> @{{message}}</p>
    </div>
    <div class="alert-box alert" ng-repeat="message in state.notifications.errorBag">
        <p><i class="lnr-cross-circle"></i> @{{message}}</p>
    </div>
</div>