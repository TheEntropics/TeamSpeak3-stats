<?php


class CounterAnalyzer extends BaseAnalyzer {

    public static function runAnalysis() {
        $fileUpload = FileManagerEventType::Upload;
        $fileDownload = FileManagerEventType::Download;
        $fileDeleted = FileManagerEventType::Delete;

        $channelCreated = ChannelEventType::ChannelCreatedEvent;
        $channelDeleted = ChannelEventType::ChannelDeletedEvent;

        $sql = "SELECT * FROM
                    (SELECT COUNT(*) as total_count FROM `client_disconnected_events`) as a,
                    (SELECT COUNT(*) AS connection_lost FROM client_disconnected_events WHERE reason = 'connection lost') as b,
                    (SELECT COUNT(*) AS leaving FROM client_disconnected_events WHERE reason = 'leaving') as c,
                    (SELECT COUNT(*) AS total_connection FROM client_connected_events) as d,
                    (SELECT COUNT(*) AS file_upload FROM file_manager_events WHERE type = $fileUpload) as e,
                    (SELECT COUNT(*) AS file_download FROM file_manager_events WHERE type = $fileDownload) as f,
                    (SELECT COUNT(*) AS file_deleted FROM file_manager_events WHERE type = $fileDeleted) as g,
                    (SELECT COUNT(*) AS channel_created FROM channel_events WHERE type = $channelCreated) as h,
                    (SELECT COUNT(*) AS channel_deleted FROM channel_events WHERE type = $channelDeleted) as i";
        $query = DB::$DB->query($sql);
        $result = $query->fetch();

        Utils::saveMiscResult("connectionLostCount", $result['connection_lost']);
        Utils::saveMiscResult("leavingCount", $result['leaving']);
        Utils::saveMiscResult("othersDisconnectCount", $result['total_count'] - $result['connection_lost'] - $result['leaving']);
        Utils::saveMiscResult("connectionCount", $result['total_connection']);
        Utils::saveMiscResult("fileUploadCount", $result['file_upload']);
        Utils::saveMiscResult("fileDownloadCount", $result['file_download']);
        Utils::saveMiscResult("fileDeletedCount", $result['file_deleted']);
        Utils::saveMiscResult("channelCreated", $result['channel_created']);
        Utils::saveMiscResult("channelDeleted", $result['channel_deleted']);
    }
}
