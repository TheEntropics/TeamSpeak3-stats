function formatTime(time) {
    seconds = time % 60;
    minutes = (time / 60 | 0) % 60;
    hours = (time / 60 / 60 | 0) % 24;
    days = time / 60 / 60 / 24 | 0;

    str = "";

    if (days > 0)
        if (days == 1) str += "1 giorno";
        else           str += days + " giorni";

    if (hours > 0)
        if (hours == 1) str += " 1 ora";
        else            str += " " + hours + " ore";

    if (minutes > 0)
        if (minutes == 1) str += " 1 minuto";
        else              str += " " + minutes + " minuti";

    if (seconds > 0)
        if (seconds == 1) str += " 1 secondo";
        else              str += " " + seconds + " secondi";

    return str;
}

function formatDate(date) {
    if (typeof(date) !== 'string')
        return "";

    var d = getUTCDate(date);

    var YYYY = padLeft(d.getFullYear(), 4);
    var MM = padLeft(d.getMonth()+1, 2);
    var DD = padLeft(d.getDate(), 2);
    var hh = padLeft(d.getHours(), 2);
    var mm = padLeft(d.getMinutes(), 2);
    var ss = padLeft(d.getSeconds(), 2);

    return DD + "/" + MM + "/" + YYYY + " alle " + hh + ":" + mm + ":" + ss;
}

function formatShortDate(date) {
    if (typeof(date) !== 'string')
        return "";

    var d = new Date(date);

    var YYYY = padLeft(d.getFullYear(), 4);
    var MM = padLeft(d.getMonth()+1, 2);
    var DD = padLeft(d.getDate(), 2);

    return DD + "/" + MM + "/" + YYYY;
}

function formatLongDate(date) {
    var MONTHS = ['gennaio', 'febbraio', 'marzo',
        'aprile', 'maggio', 'giugno',
        'luglio', 'agosto', 'settembre',
        'ottobre', 'novembre', 'dicembre'];

    var YYYY = padLeft(date.getFullYear(), 4);
    var MM = MONTHS[date.getMonth()];
    var DD = padLeft(date.getDate(), 2);

    return DD + " " + MM + " " + YYYY;
}

function padLeft(nr, n, str){
    return Array(n-String(nr).length+1).join(str||'0')+nr;
}

function getUTCDate(date) {
    return new Date(Date.UTC(
        parseInt(date.substr(0, 4)),
        parseInt(date.substr(5, 2))-1,
        parseInt(date.substr(8, 2)),
        parseInt(date.substr(11, 2)),
        parseInt(date.substr(14, 2)),
        parseInt(date.substr(17, 2))));
}
