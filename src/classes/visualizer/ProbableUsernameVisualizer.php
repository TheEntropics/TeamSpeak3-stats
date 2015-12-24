<?php


class ProbableUsernameVisualizer {
    public static function getProbableUsername($client_id) {
        $sql = "SELECT username FROM probable_username WHERE client_id = :client_id";
        $query = DB::$DB->prepare($sql);
        $query->bindValue('client_id', $client_id);
        $query->execute();
        $result = $query->fetch();
        if (!$result) return null;
        return $result['username'];
    }
}
