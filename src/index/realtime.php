<h2>Realtime <small id="realtime-delay">Caricamento...</small></h2>
<div id="realtime">
    <div class="spinner"></div>
</div>

<script>
    var refreshTTL;

    function loadRealtime() {
        console.log("Realtime update");
        $.ajax({
            url: 'realtime.php',
            dataType: 'JSON',
            success: function(channels) {
                $('.spinner').slideUp();
                $('#realtime').html(showChannel(channels, 0));
                refreshTTL = 5;
                updateRefresh();
            }
        });
    }

    function showChannel(channels, id) {
        var tag = $('<div>').addClass('channel');
        tag.append('<h4>' + channels[id].name);

        var users = $('<ul>');
        for (var i in channels[id].users) {
            var user = channels[id].users[i];

            var muted = 'normal';
            if (user.silenced) muted = 'silenced';
            if (user.muted) muted = 'muted';
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

    loadRealtime();
    setInterval(loadRealtime, 5000);
</script>
