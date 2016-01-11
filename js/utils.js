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
