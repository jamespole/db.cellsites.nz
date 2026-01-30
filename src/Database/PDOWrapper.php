<?php

declare(strict_types=1);

namespace JamesPole\DbCellsitesNz\Database;

use PDO;
use PDOStatement;
use RuntimeException;

class PDOWrapper
{
    private PDO $pdo;
    public function __construct()
    {
        $config = parse_ini_file(__DIR__ . '/../../database.ini');
        if ($config === false) {
            throw new RuntimeException();
        }
        $this->pdo = new PDO(sprintf(
            '%s:dbname=%s;host=%s;password=%s;user=%s',
            $config['driver'],
            $config['dbname'],
            $config['host'],
            $config['password'],
            $config['user']
        ));
    }
    /**
     * @param ?string[] $params
     * @return ?array<array-key,mixed>
     */
    public function getRow(string $query, ?array $params = null): ?array
    {
        if ($params === null) {
            $params = [];
        }
        $result = $this->query($query, $params)->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
                return(null);
        }
        assert(is_array($result));
        // TODO proces the array like in getRows() so that we can guarantee a return type of string[] rather tha mixed
        return($result);
    }
    /**
     * @param ?string[] $params
     * @return array<array-key,array<string,?string>>
     */
    public function getRows(string $query, ?array $params = null): array
    {
        if ($params === null) {
            $params = [];
        }
        $rows = [];
        foreach ($this->query($query, $params)->fetchAll(PDO::FETCH_ASSOC) as $thisResult) {
            $row = [];
            assert(is_array($thisResult));
            /** @psalm-suppress MixedAssignment */
            foreach ($thisResult as $thisKey => $thisValue) {
                    assert(is_string($thisKey));
                if ($thisValue === null) {
                    $row[$thisKey] = null;
                } elseif (is_string($thisValue) === true || is_int($thisValue) === true || is_float($thisValue) === true) {
                    $row[$thisKey] = $thisValue;
                }
            }
            $rows[] = $row;
        }
        return($rows);
    }
    /**
     * @param string[] $params
     */
    public function query(string $query, array $params): PDOStatement
    {
        $statement = $this->pdo->prepare($query);
        if ($statement === false) {
            throw new RuntimeException();
        }
        if ($statement->execute($params) === false) {
            throw new RuntimeException();
        }
        return($statement);
    }
}
