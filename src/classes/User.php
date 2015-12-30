<?php


class User {
    public $id;
    public $username;
    public $client_id;

    public function __construct($id, $username, $client_id) {
        $this->id = $id;
        $this->username = $username;
        $this->client_id = $client_id;
    }

    public static function fromUsername($username, $client_id=-1) {
        $sql = "SELECT * FROM users WHERE username = :username";
        if ($client_id != -1) $sql .= " AND client_id = :client_id";

        $query = DB::$DB->prepare($sql);
        $query->bindParam('username', $username);
        if ($client_id != -1) $query->bindParam('client_id', $client_id);

        $query->execute();

        $result = $query->fetchAll();
        if (count($result) == 0) return null;

        return new User($result[0]['id'], $result[0]['username'], $result[0]['client_id']);
    }

    public static function fromId($id) {
        $sql = "SELECT * FROM users WHERE id = :id";

        $query = DB::$DB->prepare($sql);
        $query->bindParam('id', $id);

        $query->execute();

        $result = $query->fetchAll();
        if (count($result) == 0) return null;

        return new User($result[0]['id'], $result[0]['username'], $result[0]['client_id']);
    }

    public static function findOrCreate($username, $client_id) {
        $user = User::fromUsername($username, $client_id);
        if ($user) return $user;

        $sql = "INSERT INTO users (username, client_id) VALUE (?, ?)";
        $query = DB::$DB->prepare($sql);

        $result = $query->execute(array($username, $client_id));
        if (!$result) return null;

        return User::fromUsername($username, $client_id);
    }

    public static function getAll() {
        $sql = "SELECT * FROM users";
        $res = DB::$DB->query($sql)->fetchAll();

        $users = array();
        foreach ($res as $user)
            $users[$user['id']] = new User($user['id'], $user['username'], $user['client_id']);
        return $users;
    }

    public static function getMasterClientId($client_id) {
        $sql = "SELECT client_id2 FROM user_collapser_results WHERE client_id1 = :client_id";
        $query = DB::$DB->prepare($sql);
        $query->bindValue("client_id", $client_id);
        $query->execute();
        return $query->fetch()['client_id2'];
    }
}
