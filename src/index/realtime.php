<div ng-controller="RealtimeCtrl">
    <h2>
        Realtime
        <small><a href="" ng-click="refresh(true)"><span class="glyphicon glyphicon-refresh"></span></a></small>
        <span ng-show="loading" class="la-ball-grid-beat la-dark la-sm">
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
        </span>
    </h2>

    <div ng-show="errored">
        <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span> Error... Retrying in 5 seconds..
    </div>

    <div ng-hide="errored">
        <div treecontrol class="tree-classic"
             tree-model="tree"
             options="treeOptions"
             expanded-nodes="expandedNodes"
             on-selection="showSelected(node)">
        <span ng-switch="node.status">
            <img ng-switch-when="away" src="img/muted/away.png">
            <img ng-switch-when="muted" src="img/muted/muted.png">
            <img ng-switch-when="silenced" src="img/muted/silenced.png">
            <img ng-switch-when="normal" src="img/muted/normal.png">
        </span>
            <a href="user.php?client-id={{node.client_id}}" ng-if="node.client_id != -1">
                {{node.name}}
            </a>
            <span ng-if="node.client_id == -1">{{node.name}}</span>
        </div>
    </div>
</div>
