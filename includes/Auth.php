<?php
class Auth
{
    private static $instance = null;
    private $user = null;

    private function __construct() {}

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function login($username, $password)
    {
        $row = db()->selectOne('users', [
            'username' => $username,
            'is_active' => 1,
            'deleted_at' => null
        ]);

        if (!$row) {
            return false;
        }

        if (!password_verify($password, $row['password_hash'])) {
            return false;
        }

        // Get roles
        $roles = db()->getAll(
            "SELECT r.name FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = ?",
            [$row['id']]
        );
        $roleNames = array_column($roles, 'name');

        $this->user = [
            'id' => $row['id'],
            'username' => $row['username'],
            'full_name' => $row['full_name'],
            'email' => $row['email'],
            'avatar_path' => $row['avatar_path'],
            'roles' => $roleNames,
        ];

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user'] = $this->user;

        logActivity('login', 'users', $row['id'], 'ورود به سیستم');

        return true;
    }

    public function logout()
    {
        if ($this->check()) {
            logActivity('logout', 'users', $this->id(), 'خروج از سیستم');
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }
        session_destroy();
        $this->user = null;
    }

    public function check()
    {
        if ($this->user !== null) {
            return true;
        }
        if (isset($_SESSION['user'])) {
            $this->user = $_SESSION['user'];
            return true;
        }
        return false;
    }

    public function user()
    {
        if (!$this->check()) {
            return null;
        }
        return $this->user;
    }

    public function id()
    {
        $u = $this->user();
        return $u ? $u['id'] : null;
    }

    public function hasRole($role)
    {
        $u = $this->user();
        if (!$u) return false;
        return in_array($role, $u['roles']);
    }
}

function auth()
{
    return Auth::getInstance();
}