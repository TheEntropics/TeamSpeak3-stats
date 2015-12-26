<div class="col-md-6">
    <h2>Realtime <small id="realtime-delay">Caricamento...</small></h2>
    <div id="realtime"></div>

    <script>
        var refreshTTL;

        function loadRealtime() {
            console.log("Realtime update");
            $.ajax({
                url: 'realtime.php',
                dataType: 'JSON',
                success: function(channels) {
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
            for (var i in channels[id].users)
                users.append("<li>" + channels[id].users[i]);
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
</div>
