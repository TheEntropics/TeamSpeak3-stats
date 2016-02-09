(function() {

    var app = angular.module('ts3stats', ['ngRoute', 'ngSanitize', 'treeControl', 'googlechart', 'rzModule']);

    app.config(['$routeProvider', function($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: 'partials/index.php'
            })
            .when('/user/:userId', {
                templateUrl: 'partials/user.php'
            })
            .otherwise({
                redirectTo: '/'
            })
    }]);

    app.factory('Utils', function() {
        return {
            formatTime: formatTime,
            formatDate: formatDate,
            formatShortDate: formatShortDate,
            formatLongDate: formatLongDate,
            getUTCDate: getUTCDate
        };
    });

    //
    // INDEX CONTROLLERS
    //

    app.controller('IndexCtrl', ['$scope', '$rootScope', '$http', 'Utils', function($scope, $rootScope, $http, Utils) {
        $rootScope.Utils = Utils;
        $rootScope.lastUpdate = false;
        $rootScope.lastUpdateErrored = false;

        $('body').removeClass('container').addClass('container-fluid');

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

    app.controller('DailyGridCtrl', ['$scope', '$rootScope', '$http', '$timeout', 'Utils', function($scope, $rootScope, $http, $timeout, Utils) {
        $scope.Utils = Utils;
        $scope.hours = []; for (var i = 0; i < 24; i++) $scope.hours.push(i);
        $scope.rows = [];
        $scope.sums = [];
        $scope.maxAvg = 0;
        $scope.startDate = null;

        $scope.loading = true;
        $scope.errored = false;
        $scope.slider = {
            minValue: 0,
            maxValue: 0,
            options: {
                floor: 0,
                ceil: 0,
                draggableRange: true,
                onChange: function() {
                    updateGrid();
                },
                translate: function(value) {
                    return Utils.formatLongDate(new Date(($scope.startDate + value*604800)*1000));
                }
            }
        };

        var days = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];

        $scope.reload = function() {
            $scope.loading = true;

            $http({
                method: 'GET',
                url: 'api/index/daily.php'
            }).then(function(response) {
                saveGrid(response.data);
                $timeout(function () {
                    $scope.$broadcast('rzSliderForceRender');
                });
                updateGrid();
                $scope.loading = false;
                $scope.errored = false;
            }, function() {
                $scope.loading = false;
                $scope.errored = true;
            });
        };

        var saveGrid = function(data) {
            var sums = [];
            for (var i = 0; i < 24*7; i++) {
                sums[i] = 0;
                $scope.sums[i] = [];
            }

            var addHour = function(timestamp, avg) {
                var date = new Date(timestamp*1000);
                var week = date.getDay();
                var hour = date.getHours();

                var cell_id = week*24 + hour;
                var prefix = sums[cell_id] = sums[cell_id] + avg;
                $scope.sums[cell_id].push(prefix);
                $scope.maxAvg = Math.max($scope.maxAvg, avg);
            };

            for (var i in data) {
                var prev = i > 0 ? data[i-1].timestamp + 3600 : 0;
                while (i > 0 && prev < data[i].timestamp) {
                    var timestamp = prev;
                    var avg = 0;
                    addHour(timestamp, avg);

                    prev += 3600;
                }
                var timestamp = data[i].timestamp;
                var avg = data[i].average;

                addHour(timestamp, avg);
            }

            $scope.slider.options.ceil = $scope.slider.maxValue = $scope.sums[0].length - 1;
            $scope.slider.minValue = Math.max(0, $scope.slider.maxValue-4);
            $scope.startDate = data[0].timestamp;
        };

        var updateGrid = function() {

            var interpolate = function (from, to, t) {
                return from * (1 - t) + to * t;
            };

            var getColor = function(value, max) {
                var start = [ 208, 1.0, 0.64 ];
                var end = [ 0, 1.0, 0.64 ];

                var t = Math.min(value / Math.max(3, max), 1.0);

                return 'hsl(' +
                    interpolate(start[0], end[0], t) + ',' +
                    interpolate(start[1], end[1], t)*100 + '%,' +
                    interpolate(start[2], end[2], t)*100 + '%)';
            };

            var getAverage = function(cell_id) {
                var sums = $scope.sums[cell_id];
                var min = $scope.slider.minValue;
                var max = $scope.slider.maxValue;

                if (min > 0)
                    return (sums[max] - sums[min-1]) / (max-min+1);
                return sums[max] / (max-min+1);
            };

            var max = 0;

            for (var i in days) {
                var row = {
                    day: days[i],
                    cells: []
                };
                for (var j = 0; j < 24; j++) {
                    var avg = getAverage(i*24+j);
                    max = Math.max(max, avg);
                    row.cells.push({
                        value: avg
                    });
                }
                $scope.rows[((i|0)+6)%7] = row;
            }

            max = (max * 3 + $scope.maxAvg) / 4;

            for (var i in days)
                for (var j = 0; j < 24; j++)
                    $scope.rows[i].cells[j].color = getColor($scope.rows[i].cells[j].value, max);
        };

        $scope.reload();
    }]);

    //
    // USER CONTROLLERS
    //

    app.controller('UserCtrl', [function() {
        $('body').removeClass('container-fluid').addClass('container');
    }]);

    app.controller('UserInfoCtrl', ['$scope', '$routeParams', '$location', '$http', 'Utils', function($scope, $routeParams, $location, $http, Utils) {
        $scope.Utils = Utils;
        $scope.info = {};
        $scope.loading = true;

        $http({
            method: 'GET',
            url: 'api/user/info.php',
            params: { 'client_id': $routeParams.userId }
        }).then(function(response) {
            $scope.info = response.data;
            if ($scope.info.online)
                setInterval(updateTime, 1000);
            $scope.loading = false;
        }, function() {
            $location.path('/');
        });

        var updateTime = function() {
            if (!$scope.info.online) return;

            var online_since = Utils.getUTCDate($scope.info.online_since.date);
            var current_session = Math.floor((new Date() - online_since) / 1000);
            $scope.info.uptime = current_session;
            $scope.$apply();
        };

    }]);

    app.controller('UserLogCtrl', ['$scope', '$http', '$routeParams', 'Utils', function($scope, $http, $routeParams, Utils) {
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
                url: 'api/user/log.php',
                params: { offset: off, limit: lim, client_id: $routeParams.userId }
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

        $scope.loadOthers();
    }]);

    app.controller('UserUsernameCtrl', ['$scope', '$http', '$routeParams', 'Utils', function($scope, $http, $routeParams, Utils) {
        $scope.Utils = Utils;
        $scope.usernames = [];

        $http({
            method: 'GET',
            url: 'api/user/usernames.php',
            // no limit in number of usernames...
            params: { limit: 100000, client_id: $routeParams.userId }
        }).then(function(response) {
            $scope.usernames = response.data;
        });
    }]);

    app.controller('UserStreakCtrl', ['$scope', '$http', '$routeParams', 'Utils', function($scope, $http, $routeParams, Utils) {
        $scope.Utils = Utils;
        $scope.streak = {};
        $scope.errored = false;
        $scope.loading = true;

        $http({
            method: 'GET',
            url: 'api/user/streak.php',
            params: { client_id: $routeParams.userId }
        }).then(function(response) {
            $scope.streak = response.data;
            $scope.errored = false;
            $scope.loading = false;
        }, function() {
            $scope.errored = true;
            $scope.loading = false;
        });
    }]);

    app.controller('UserDailyGraphCtrl', ['$scope', '$http', '$routeParams', 'Utils', 'googleChartApiConfig', function($scope, $http, $routeParams, Utils, googleChartApiConfig) {
        googleChartApiConfig.optionalSettings = { packages: ['corechart', 'calendar'] };
        googleChartApiConfig.version = '1.1';

        $scope.errored = false;
        $scope.loading = true;

        $scope.chartObject = {
            type: 'Calendar',
            options: {
                height: 350
            },
            data: {
                cols: [
                    { id: 'Date', type: 'date' },
                    { id: 'Seconds', type: 'number' },
                    { id: 'Text', type: 'string', role: 'tooltip', p: {html: true} }
                ],
                rows: []
            }
        };

        $http({
            method: 'GET',
            url: 'api/user/daily.php',
            params: { client_id: $routeParams.userId }
        }).then(function(response) {
            var rows = $scope.chartObject.data.rows;
            for (var i in response.data) {
                var date = new Date(response.data[i].date);
                var time = response.data[i].day_time;

                rows.push({
                    c: [
                        { v: date },
                        { v: time },
                        { v: "<div><h4>" + Utils.formatLongDate(date) + "</h4><p>" + Utils.formatTime(time) + "</p></div>" }
                    ]
                })
            }

            $scope.loading = false;
            $scope.errored = false;
        }, function() {
            $scope.loading = false;
            $scope.errored = true;
        });
    }]);

})();
