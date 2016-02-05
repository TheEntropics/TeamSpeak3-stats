<p ng-show="!lastUpdateErrored && lastUpdate">Ultimo aggiornamento {{Utils.formatDate(lastUpdate)}}</p>
<p ng-show="lastUpdateErrored"><span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span> Error</p>
