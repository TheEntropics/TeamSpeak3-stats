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

    private static $autoIncrement = null;
    private static $cache = array();
    private static $createCache = array();

    public function __construct($id, $username, $client_id, $client_id2 = null) {
        $this->id = $id == -1 ? User::$autoIncrement++ : $id;
        $this->username = $username;
        $this->client_id = $client_id;
        if ($client_id2 == null)
            $client_id2 = $client_id;
        $this->master_client_id = $client_id2;
    }

    /**
     * Return a user from its username
     * @param $username The username to search to
     * @param int $client_id Manually specify the client_id, -1 to select the first
     * @return User|null
     */
    public static function fromUsername($username, $client_id=-1) {
        if (User::$autoIncrement == null)
            User::buildCache();

        if ($client_id == -1)
            return count(User::$cache[$username]) > 0 ? array_values(User::$cache[$username])[0] : null;
        else
            return isset(User::$cache[$username][$client_id]) ? User::$cache[$username][$client_id] : null;
    }

    /**
     * Find or create the user from the username and the client_id
     * @param $username Username of the user
     * @param $client_id Client id of the user
     * @return User|null
     */
    public static function findOrCreate($username, $client_id) {
        $user = User::fromUsername($username, $client_id);
        if ($user) return $user;

        User::create(new User(-1, $username, $client_id));

        return User::fromUsername($username, $client_id);
    }

    /**
     * Return a list of all users
     * @return array
     */
    public static function getAll() {
        $sql = "SELECT * FROM users LEFT JOIN user_collapser_results ON users.client_id = user_collapser_results.client_id1";
        $res = DB::$DB->query($sql)->fetchAll();

        $users = array();
        foreach ($res as $user)
            $users[$user['id']] = new User($user['id'], $user['username'], $user['client_id'], $user['client_id2']);
        return $users;
    }

    /**
     * Return the master_client_id of a user
     */
    public static function getMasterClientId($client_id) {
        $sql = "SELECT client_id2 FROM user_collapser_results WHERE client_id1 = :client_id";
        $query = DB::$DB->prepare($sql);
        $query->bindValue("client_id", $client_id);
        $query->execute();
        return $query->fetch()['client_id2'];
    }

    /**
     * Prepare the cache of the users..
     */
    public static function buildCache() {
        User::$cache = array();
        User::$createCache = array();
        User::$autoIncrement = 1 + DB::$DB->query("SELECT MAX(id) FROM users")->fetch()[0];

        $users = User::getAll();

        foreach ($users as $user) {
            $username = $user->username;
            $client_id = $user->client_id;

            if (!isset(User::$cache[$username]))
                User::$cache[$username] = array();
            User::$cache[$username][$client_id] = $user;
        }
    }

    /**
     * Create a new user using the internal cache. It's important to flush the user cache after creating all the users
     * @param $user User The user to create
     */
    private static function create($user) {
        User::$createCache[] = $user;
        User::$cache[$user->username][$user->client_id] = $user;
    }

    /**
     * Flush the user create cache inserting the users into the database
     * @param null|array $users A list of the users to insert into the db. If null the internal cache is flushed
     */
    public static function flushCreateCache($users = null) {
        if (is_null($users))
            User::flushCreateCache(User::$createCache);
        else if (count($users) > 500) {
            $chunks = array_chunk($users, 500);
            foreach ($chunks as $chunk)
                User::flushCreateCache($chunk);
        } else if (count($users) > 0) {
            $sql = "INSERT INTO users (id, username, client_id) VALUES ";
            $chunks = array();
            foreach ($users as $user)
                $chunks[] = "(" . $user->id . ", " . DB::$DB->quote($user->username) . ", " . $user->client_id . ")";
            $sql .= implode(", ", $chunks);

            DB::$DB->query($sql);
        }
    }
}
