<?php

class EventType {
    /**
     * A client has been connected
     */
    const ClientConnected       = 1;
    /**
     * A client has been disconnected
     */
    const ClientDisconnected    = 2;
    /**
     * A file was uploaded/downloaded/removed
     */
    const FileManager           = 3;
    /**
     * A channel was created/removed
     */
    const Channel               = 4;
    /**
     * The server was started
     */
    const ServerStarted         = 5;
}
