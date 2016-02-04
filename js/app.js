(function() {

    var app = angular.module('ts3stats', ['ngSanitize', 'treeControl']);

    app.controller('MainCtrl', ['$scope', '$rootScope', function($scope, $rootScope) {
        $rootScope.spinnerColors = ['#2196F3', '#1DD2AF', '#E74C3C', '#34495E', '#F39C12'];
        $rootScope.spinnerIndex = 0;
    }]);

    app.controller('ScoreboardCtrl', ['$scope', '$rootScope', 'Utils', function($scope, $rootScope, Utils) {
        $scope.Utils = Utils;
        $scope.users = [];
        $scope.loading = false;
        $scope.spinnerIndex = 0;

        var offset = 0;
        var limitPerRequest = 10;

        $scope.loadOthers = function(off, lim) {
            $scope.loading = true;
            $scope.spinnerIndex = ($scope.spinnerIndex + 1) % $rootScope.spinnerColors.length;

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
                        $scope.loading = false;
                    });
                    offset += limitPerRequest;
                },
                error: function () {
                    alert("Error!");
                    $scope.$apply(function() {
                        $scope.loading = false;
                    });
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
        $scope.loading = false;
        $scope.spinnerIndex = 0;

        var offset = 0;
        var limitPerRequest = 10;

        $scope.loadOthers = function(off, lim) {
            $scope.loading = true;
            $scope.spinnerIndex = ($scope.spinnerIndex + 1) % $rootScope.spinnerColors.length;

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
                        $scope.loading = false;
                    });
                    offset += limitPerRequest;
                },
                error: function () {
                    alert("Error!");
                    $scope.$apply(function() {
                        $scope.loading = false;
                    });
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

    app.controller('CounterCtrl', ['$scope', '$rootScope', 'Utils', function($scope, $rootScope, Utils) {
        $scope.Utils = Utils;
        $scope.counter = {};
        $scope.loading = false;
        $scope.spinnerIndex = 0;

        $scope.reload = function() {
            $scope.loading = true;
            $scope.spinnerIndex = ($scope.spinnerIndex + 1) % $rootScope.spinnerColors.length;
            $.ajax({
                url: 'api/index/count.php',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    $scope.$apply(function() {
                        $scope.counter = data;
                        $scope.loading = false;
                    });
                },
                error: function (err) {
                    alert("Error!");
                    $scope.$apply(function() {
                        $scope.loading = false;
                    });
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
        $scope.errored = false;
        $scope.loading = true;
        $scope.spinnerIndex = 0;

        var onlineUsers = null;

        $scope.refresh = function(x) {
            // when refresh is called for loading the page for the first time
            // the $scope.$apply call is impossible because an other $apply
            // call was done in the constructor of the controller
            if (!x) {
                $scope.$apply(function() {
                    $scope.loading = true;
                    $scope.errored = false;
                    $scope.spinnerIndex = ($scope.spinnerIndex + 1) % $rootScope.spinnerColors.length;
                });
            } else {
                $scope.loading = true;
                $scope.errored = false;
                $scope.spinnerIndex = ($scope.spinnerIndex + 1) % $rootScope.spinnerColors.length;
            }
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
                        $scope.errored = false;
                        $scope.loading = false;
                    });
                }, error: function (err) {
                    console.error("Error in realtime", err);
                    $scope.$apply(function() {
                        $scope.errored = true;
                        $scope.loading = false;
                    });
                }
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

    app.controller('DailyGridCtrl', ['$scope', '$rootScope', function($scope, $rootScope) {
        $scope.hours = []; for (var i = 0; i < 24; i++) $scope.hours.push(i);
        $scope.rows = [];
        $scope.loading = true;
        $scope.spinnerIndex = 0;

        var days = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'];

        $scope.reload = function() {
            $scope.loading = true;
            $scope.spinnerIndex = ($scope.spinnerIndex + 1) % $rootScope.spinnerColors.length;
            $.ajax({
                url: 'api/index/daily.php',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    $scope.$apply(function() {
                        applyGrid(data);
                        $scope.loading = false;
                    });
                },
                error: function () {
                    alert("Error!");
                    $scope.$apply(function() {
                        $scope.loading = false;
                    });
                }
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
    }]);

    app.factory('Utils', function() {
        return {
            formatTime: formatTime,
            formatDate: formatDate
        };
    });

})();
