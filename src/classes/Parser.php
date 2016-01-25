<?php


class Parser {

    const Matchers = array(
        "ClientConnectedEvent" =>    "/^([^|]*)\\|[^|]*\\|[^|]*\\|[^|]*\\| client connected '([^']+)'\\(id:(\\d+)\\) from ([\\d.]+):\\d+$/",
        "ClientDisconnectedEvent" => "/^([^|]*)\\|[^|]*\\|[^|]*\\|[^|]*\\| client disconnected '([^']+)'\\(id:(\\d+)\\) reason '.*reasonmsg(?:=(.+?))?(?: bantime=(\\d+))?'/",
        "FileManagerEvent" =>        "/^([^|]*)\\|[^|]*\\|[^|]*\\|[^|]*\\| file (upload|download|deleted) .* by client '([^']+)'\\(id:(\\d+)\\)$/",
        "ChannelEvent" =>            "/^([^|]*)\\|[^|]*\\|[^|]*\\|[^|]*\\| channel '([^']+)'\\(id:\\d+\\) (created|deleted)(?: as sub channel of '[^']+'\\(id:\\d+\\))? by '([^']+)'\\(id:(\\d+)\\)$/"
    );

    /**
     * Parse a log line into an instance of Event
     */
    public static function parseLine($line) {
        $matches = array();
        foreach (Parser::Matchers as $eventName => $regex)
            if (preg_match($regex, $line, $matches))
                return new $eventName($matches);
        return null;
    }
}
