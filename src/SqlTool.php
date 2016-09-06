<?php
namespace Dyike\SqlTool;

use InvalidArgumentException;

class SqlTool
{
    protected $db;
    protected $host;
    protected $dbName;
    protected $dbUser;
    protected $dbPass;
    protected $port;

    protected $pdo;

    public function __construct($db, $host, $dbName, $port, $dbUser, $dbPass)
    {
        $this->db = $db;
        $this->host = $host;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
        $this->port = $port;

    }


    public function getTables()
    {
        $pdo = new PDO("$this->db:host=$this->host;dbname=$this->dbName;port=$this->port", $this->dbUser, $this->dbPass);
        echo "$this->host" . "Contected !";exit;
        $res = $pdo->query('show tables')->fetchAll(PDO::FETCH_ASSOC);
        $table = array_column($res, "'Tables_in_' . $this->dbName");
        return $table;
    }

    public function getFields()
    {

    }












}
$sql = new SqlTool('mysql', '192.168.33.10', 'patient', '3306', 'root', '123456yf');

$table = $sql->getTables();

print_r($table);






