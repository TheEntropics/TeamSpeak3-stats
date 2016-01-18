<?php


class User {
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $username;
    /**
     * @var int
     */
    public $client_id;
    /**
     * @var int|null
     */
    public $master_client_id;

    public function __construct($id, $username, $client_id, $client_id2 = null) {
        $this->id = $id;
        $this->username = $username;
        $this->client_id = $client_id;
        if ($client_id2 == null)
            $client_id2 = $client_id;
        $this->master_client_id = $client_id2;
    }

    public static function fromUsername($username, $client_id=-1) {
        $sql = "SELECT * FROM users LEFT JOIN user_collapser_results ON users.client_id = user_collapser_results.client_id1 WHERE username = :username";
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
        $sql = "SELECT * FROM users LEFT JOIN user_collapser_results ON users.client_id = user_collapser_results.client_id1 WHERE id = :id";

        $query = DB::$DB->prepare($sql);
        $query->bindParam('id', $id);

        $query->execute();

        $result = $query->fetchAll();
        if (count($result) == 0) return null;

        return new User($result[0]['id'], $result[0]['username'], $result[0]['client_id'], $result[0]['client_id2']);
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
        $sql = "SELECT * FROM users LEFT JOIN user_collapser_results ON users.client_id = user_collapser_results.client_id1";
        $res = DB::$DB->query($sql)->fetchAll();

        $users = array();
        foreach ($res as $user)
            $users[$user['id']] = new User($user['id'], $user['username'], $user['client_id'], $user['client_id2']);
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
