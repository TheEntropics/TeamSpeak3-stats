<div ng-controller="RealtimeCtrl">
    <h2>Realtime</h2>
    <div class="spinner"></div>
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
