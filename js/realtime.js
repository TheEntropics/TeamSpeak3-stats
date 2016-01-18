$(function() {
    var refreshTTL;

    var onlineUsers = null;

    function loadRealtime() {
        console.log("Realtime update");
        $.ajax({
            url: 'api/index/realtime.php',
            dataType: 'JSON',
            success: function(channels) {
                checkForOnlineUsers(channels);
                $('.spinner').slideUp();
                $('#realtime').html(showChannel(channels, 0));
                refreshTTL = 5;
                updateRefresh();
            }
        });
    }

    function checkForOnlineUsers(channels) {
        var users = getOnlineUsers(channels, 0);

        if (onlineUsers == null || onlineUsers.sort().join('|') == users.sort().join('|')) {
            onlineUsers = users;
            return;
        }
        setTimeout(function() { location.reload(); }, 2000);
    }

    function getOnlineUsers(channels, id) {
        var users = [];
        for (var i in channels[id].users)
            users.push(channels[id].users[i].client_id);
        for (var i in channels[id].channels)
            users = users.concat(getOnlineUsers(channels, channels[id].channels[i]));
        return users;
    }

    function showChannel(channels, id) {
        var tag = $('<div>').addClass('channel');
        tag.append('<h4>' + channels[id].name);

        var users = $('<ul>');
        for (var i in channels[id].users) {
            var user = channels[id].users[i];

            var muted = 'normal';
            if (user.muted) muted = 'muted';
            if (user.silenced) muted = 'silenced';
            if (user.away) muted = 'away';

            var away_message = '';
            if (user.away_message.length > 0)
                away_message = ' <i>[' + user.away_message + ']</i>';

            var user = '<a href="user.php?client-id=' + user.client_id + '">' + user.name + "</a>"

            var img = "<img src='img/muted/"+muted+".png' /> ";
            users.append("<li>" + img + user + away_message);
        }
        tag.append(users);

        for (var i in channels[id].channels)
            tag.append(showChannel(channels, channels[id].channels[i]));

        return tag;
    }
    
    function updateRefresh() {
        $('#realtime-delay').text('Refresh in ' + refreshTTL + ' sec');
        --refreshTTL;
        if (refreshTTL > 0)
            setTimeout(updateRefresh, 1000);
    }

    function updateUptime() {
        $('li[data-online="true"]').each(function() {
            var $this = $(this);
            var prev_uptime = $this.attr('data-uptime') | 0;
            var online_since = new Date($this.attr('data-online-since'));
            var current_session = (new Date() - online_since)/1000 | 0;
            var new_uptime = formatTime(prev_uptime+current_session);
            $this.find('.uptime').text(new_uptime);
        });
    }

    loadRealtime();
    setInterval(updateUptime, 1000);
    setInterval(loadRealtime, 5000);
})
