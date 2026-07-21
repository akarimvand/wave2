<?php
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die('Database connection failed: ' . $e->getMessage());
            } else {
                die('Database connection failed.');
            }
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function getOne($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    public function select($table, $where = [], $orderBy = '', $limit = '', $offset = 0)
    {
        $sql = "SELECT * FROM `{$table}`";
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $col => $val) {
                $colQuoted = "`{$col}`";
                if (is_array($val)) {
                    $placeholders = implode(',', array_fill(0, count($val), '?'));
                    $conditions[] = "{$colQuoted} IN ({$placeholders})";
                    $params = array_merge($params, array_values($val));
                } elseif ($val === null) {
                    $conditions[] = "{$colQuoted} IS NULL";
                } else {
                    $conditions[] = "{$colQuoted} = ?";
                    $params[] = $val;
                }
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->getAll($sql, $params);
    }

    public function selectOne($table, $where = [], $orderBy = '')
    {
        $results = $this->select($table, $where, $orderBy, 1);
        return $results ? $results[0] : null;
    }

    public function count($table, $where = [])
    {
        $sql = "SELECT COUNT(*) as total FROM `{$table}`";
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $col => $val) {
                $colQuoted = "`{$col}`";
                if (is_array($val)) {
                    $placeholders = implode(',', array_fill(0, count($val), '?'));
                    $conditions[] = "{$colQuoted} IN ({$placeholders})";
                    $params = array_merge($params, array_values($val));
                } elseif ($val === null) {
                    $conditions[] = "{$colQuoted} IS NULL";
                } else {
                    $conditions[] = "{$colQuoted} = ?";
                    $params[] = $val;
                }
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $row = $this->getOne($sql, $params);
        return (int) $row['total'];
    }

    public function insert($table, $data)
    {
        $columns = implode(', ', array_map(function($c){ return "`{$c}`"; }, array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO `{$table}` ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where)
    {
        $setParts = [];
        $params = [];
        foreach ($data as $col => $val) {
            $setParts[] = "`{$col}` = ?";
            $params[] = $val;
        }

        $whereParts = [];
        foreach ($where as $col => $val) {
            $whereParts[] = "`{$col}` = ?";
            $params[] = $val;
        }

        $sql = "UPDATE `{$table}` SET " . implode(', ', $setParts) . " WHERE " . implode(' AND ', $whereParts);
        return $this->query($sql, $params)->rowCount();
    }

    public function updateById($table, $id, $data)
    {
        return $this->update($table, $data, ['id' => $id]);
    }

    public function delete($table, $where)
    {
        if (is_numeric($where)) {
            $where = ['id' => $where];
        }

        $whereParts = [];
        $params = [];
        foreach ($where as $col => $val) {
            $whereParts[] = "`{$col}` = ?";
            $params[] = $val;
        }

        $sql = "DELETE FROM `{$table}` WHERE " . implode(' AND ', $whereParts);
        return $this->query($sql, $params)->rowCount();
    }

    public function softDelete($table, $id)
    {
        return $this->updateById($table, $id, [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->pdo->commit();
    }

    public function rollback()
    {
        $this->pdo->rollBack();
    }

    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}

function db()
{
    return Database::getInstance();
}