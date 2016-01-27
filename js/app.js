(function() {

    var app = angular.module('ts3stats', ['ngSanitize', 'treeControl']);

    app.controller('ScoreboardCtrl', ['$scope', '$rootScope', 'Utils', function($scope, $rootScope, Utils) {
        $scope.Utils = Utils;
        $scope.users = [];

        var offset = 0;
        var limitPerRequest = 10;

        $scope.loadOthers = function(off, lim) {
            if (off === undefined) off = offset;
            if (lim === undefined) lim = limitPerRequest;
            $.ajax({
                url: 'api/index/scoreboard.php',
                method: 'GET',
                data: {offset: off, limit: lim},
                dataType: 'json',
                success: function (data) {
                    $scope.$apply(function() {
                        $scope.users = $scope.users.concat(data);
                    });
                    offset += limitPerRequest;
                },
                error: function () {
                    alert("Error!");
                }
            });
        };

        var updateTimes = function() {
            $scope.$apply(function() {
                for (var i in $scope.users) {
                    var user = $scope.users[i];
                    if (!user.online) continue;

                    var prev_uptime = user.total_uptime | 0;
                    var online_since = new Date(user.onlineSince.date + " UTC");
                    var current_session = Math.floor((new Date() - online_since)/1000);
                    var new_uptime = prev_uptime + current_session;
                    user.uptime = new_uptime;
                }
            });
        };

        $rootScope.reloadScoreboard = function() {
            $scope.users = [];
            $scope.loadOthers(0, offset);
            offset -= limitPerRequest;
        };

        $scope.loadOthers();

        setInterval(updateTimes, 1000);
    }]);

    app.controller('LogCtrl', ['$scope', '$rootScope', 'Utils', function($scope, $rootScope, Utils) {
        $scope.Utils = Utils;
        $scope.logs = [];

        var offset = 0;
        var limitPerRequest = 10;

        $scope.loadOthers = function(off, lim) {
            if (off === undefined) off = offset;
            if (lim === undefined) lim = limitPerRequest;
            $.ajax({
                url: 'api/index/log.php',
                method: 'GET',
                data: {offset: off, limit: lim},
                dataType: 'json',
                success: function (data) {
                    $scope.$apply(function() {
                        $scope.logs = $scope.logs.concat(data);
                    });
                    offset += limitPerRequest;
                },
                error: function () {
                    alert("Error!");
                }
            });
        };

        $rootScope.reloadLog = function() {
            $scope.logs = [];
            $scope.loadOthers(0, offset);
            offset -= limitPerRequest;
        };

        $scope.loadOthers();
    }]);

    app.controller('CounterCtrl', ['$scope', 'Utils', function($scope, Utils) {
        $scope.Utils = Utils;
        $scope.counter = {};

        $scope.reload = function() {
            $.ajax({
                url: 'api/index/count.php',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    $scope.$apply(function() {
                        $scope.counter = data;
                    });
                },
                error: function (err) {
                    alert("Error!", err);
                }
            });
        };

        $scope.reload();
    }]);

    app.controller('RealtimeCtrl', ['$scope', '$rootScope', function($scope, $rootScope) {
        $scope.treeOptions = {
            nodeChildren: "channels",
            dirSelectable: false
        };
        $scope.tree = [];
        $scope.expandedNodes = [];

        var onlineUsers = {};

        $scope.refresh = function() {
            $.ajax({
                url: 'api/index/realtime.php',
                dataType: 'JSON',
                success: function (channels) {
                    $('.spinner').remove();

                    var tree = buildTree("0", channels);
                    checkForOnlineUsers(channels);
                    $scope.$apply(function() {
                        $scope.tree = tree;
                        $scope.expandedNodes = tree.slice();
                    });
                }, error: function (err) {
                    console.error("Error in realtime", err);
                }
            });
        };

        $scope.refresh();

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

            $rootScope.reloadScoreboard();
            $rootScope.reloadLog();
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

    app.factory('Utils', function() {
        return {
            formatTime: formatTime,
            formatDate: formatDate
        };
    });

})();
