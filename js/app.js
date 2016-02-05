(function() {

    var app = angular.module('ts3stats', ['ngSanitize', 'treeControl']);

    app.controller('MainCtrl', ['$scope', '$rootScope', '$http', 'Utils', function($scope, $rootScope, $http, Utils) {
        $rootScope.Utils = Utils;
        $rootScope.lastUpdate = false;
        $rootScope.lastUpdateErrored = false;

        $rootScope.reloadLastUpdate = function() {
            $http({
                method: 'GET',
                url: 'api/index/lastUpdate.php'
            }).then(function(response) {
                $rootScope.lastUpdate = response.data.date;
                $rootScope.lastUpdateErrored = false;
            }, function() {
                $rootScope.lastUpdateErrored = true;
            });
        };

        $rootScope.reloadLastUpdate();
    }]);

    app.controller('ScoreboardCtrl', ['$scope', '$rootScope', '$http', 'Utils', function($scope, $rootScope, $http, Utils) {
        $scope.Utils = Utils;
        $scope.users = [];
        $scope.loading = false;
        $scope.errored = false;

        var offset = 0;
        var limitPerRequest = 10;

        $scope.loadOthers = function(refresh) {
            $scope.loading = true;

            var off = offset;
            var lim = limitPerRequest;
            if (refresh) {
                off = 0;
                lim = offset;
            }

            $http({
                method: 'GET',
                url: 'api/index/scoreboard.php',
                params: {offset: off, limit: lim}
            }).then(function(response) {
                $scope.loading = false;
                $scope.errored = false;
                if (refresh)
                    $scope.users = response.data;
                else {
                    $scope.users = $scope.users.concat(response.data);
                    offset += limitPerRequest;
                }
            }, function() {
                $scope.loading = false;
                $scope.errored = true;
            });
        };

        var updateTimes = function() {
            for (var i in $scope.users) {
                var user = $scope.users[i];
                if (!user.online) continue;

                var prev_uptime = user.total_uptime | 0;
                var online_since = Utils.getUTCDate(user.onlineSince.date);
                var current_session = Math.floor((new Date() - online_since) / 1000);
                var new_uptime = prev_uptime + current_session;
                user.uptime = new_uptime;
            }
            $scope.$apply();
        };

        $rootScope.reloadScoreboard = function() {
            $scope.loadOthers(true);
            $rootScope.reloadLastUpdate();
        };

        $scope.loadOthers();

        setInterval(updateTimes, 1000);
        setInterval($rootScope.reloadScoreboard, 10000);
    }]);

    app.controller('LogCtrl', ['$scope', '$rootScope', '$http', 'Utils', function($scope, $rootScope, $http, Utils) {
        $scope.Utils = Utils;
        $scope.logs = [];
        $scope.loading = false;
        $scope.errored = false;

        var offset = 0;
        var limitPerRequest = 10;

        $scope.loadOthers = function(refresh) {
            $scope.loading = true;

            var off = offset;
            var lim = limitPerRequest;
            if (refresh) {
                off = 0;
                lim = offset;
            }

            $http({
                method: 'GET',
                url: 'api/index/log.php',
                params: {offset: off, limit: lim}
            }).then(function(response) {
                $scope.loading = false;
                $scope.errored = false;
                if (refresh)
                    $scope.logs = response.data;
                else {
                    $scope.logs = $scope.logs.concat(response.data);
                    offset += limitPerRequest;
                }
            }, function() {
                $scope.loading = false;
                $scope.errored = true;
            });
        };

        $rootScope.reloadLog = function() {
            $scope.loadOthers(true);
            $rootScope.reloadLastUpdate();
        };

        $scope.loadOthers();

        setInterval($rootScope.reloadLog, 10000);
    }]);

    app.controller('CounterCtrl', ['$scope', '$http', 'Utils', function($scope, $http, Utils) {
        $scope.Utils = Utils;
        $scope.counter = {};
        $scope.loading = false;
        $scope.errored = false;

        $scope.reload = function() {
            $scope.loading = true;

            $http({
                method: 'GET',
                url: 'api/index/count.php'
            }).then(function(response) {
                $scope.loading = false;
                $scope.errored = false;
                $scope.counter = response.data;
            }, function() {
                $scope.loading = false;
                $scope.errored = true;
            });
        };

        $scope.reload();

        setTimeout($scope.reload, 10000);
    }]);

    app.controller('RealtimeCtrl', ['$scope', '$rootScope', '$http', function($scope, $rootScope, $http) {
        $scope.treeOptions = {
            nodeChildren: "channels",
            dirSelectable: false
        };
        $scope.tree = [];
        $scope.expandedNodes = [];
        $scope.errored = false;
        $scope.loading = false;

        var onlineUsers = null;

        $scope.refresh = function(x) {
            // when refresh is called for loading the page for the first time
            // the $scope.$apply call is impossible because an other $apply
            // call was done in the constructor of the controller
            $scope.loading = true;
            if (!x) $scope.$apply();

            $http({
                method: 'GET',
                url: 'api/index/realtime.php'
            }).then(function(response) {
                $scope.loading = false;
                $scope.errored = false;
                var channels = response.data;
                var tree = buildTree("0", channels);
                checkForOnlineUsers(channels);
                $scope.tree = tree;
                $scope.expandedNodes = tree.slice();
                $scope.errored = false;
                $scope.loading = false;
            }, function() {
                $scope.loading = false;
                $scope.errored = true;
            });
        };

        $scope.refresh(true);

        setInterval($scope.refresh, 5000);

        var buildTree = function(curr, channels) {
            var node = [];
            var channel = channels[curr];

            for (var i in channel.users) {
                var user = channel.users[i];
                node.push(getUserNode(user));
            }
            for (var i in channel.channels) {
                var child = channel.channels[i];
                var children = buildTree(child, channels);
                node.push(getChannelNode(channels[child], children));
            }

            return node;
        };

        var getUserNode = function(user) {
            return {
                client_id: user.client_id,
                name: user.name,
                status: user.away ? 'away' : (user.silenced ? 'silenced' : (user.muted ? 'muted' : 'normal')),
                channels: []
            };
        };

        var getChannelNode = function(channel, channels) {
            return {
                client_id: -1,
                name: channel.name,
                status: 'channel',
                channels: channels
            };
        };

        var checkForOnlineUsers = function(channels) {
            var users = getOnlineUsers(channels, 0);

            if (onlineUsers == null || onlineUsers.sort().join('|') == users.sort().join('|')) {
                onlineUsers = users;
                return;
            }

            onlineUsers = users;
            setTimeout(function() {
                $rootScope.reloadScoreboard();
                $rootScope.reloadLog();
                $scope.$apply();
            }, 2000);
        };

        var getOnlineUsers = function(channels, id) {
            var users = [];
            for (var i in channels[id].users)
                users.push(channels[id].users[i].client_id);
            for (var i in channels[id].channels)
                users = users.concat(getOnlineUsers(channels, channels[id].channels[i]));
            return users;
        };

    }]);

    app.controller('DailyGridCtrl', ['$scope', '$rootScope', '$http', function($scope, $rootScope, $http) {
        $scope.hours = []; for (var i = 0; i < 24; i++) $scope.hours.push(i);
        $scope.rows = [];
        $scope.loading = true;
        $scope.spinnerIndex = 0;

        var days = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'];

        $scope.reload = function() {
            $scope.loading = true;

            $http({
                method: 'GET',
                url: 'api/index/daily.php'
            }).then(function(response) {
                $scope.loading = false;
                $scope.errored = false;
                applyGrid(response.data);
            }, function() {
                $scope.loading = false;
                $scope.errored = true;
            });
        };

        var applyGrid = function(data) {
            $scope.rows = [];
            for (d in data) {
                var cells = data[d];
                var day = days[d];

                $scope.rows[d] = { day: day, cells: cells };
            }
        };

        $scope.reload();

        setTimeout($scope.reload, 10000);
    }]);

    app.factory('Utils', function() {
        return {
            formatTime: formatTime,
            formatDate: formatDate,
            getUTCDate: getUTCDate
        };
    });

})();
