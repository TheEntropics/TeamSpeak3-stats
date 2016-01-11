(function(){
    function updateUptime() {
       $('.uptime').each(function() {
           var $this = $(this);
           var online_since = new Date($this.attr('data-online-since'));
           var current_session = (new Date() - online_since)/1000 | 0;
           $this.text(formatTime(current_session));
       });
    }
    setInterval(updateUptime, 1000);
})();
