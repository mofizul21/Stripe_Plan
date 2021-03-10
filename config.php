<?php
// Subscription plans 
// Minimum amount is $0.50 US 
// Interval day, week, month or year 
$plans = array(
    '1' => array(
        'name' => 'Weekly Subscription',
        'price' => 25,
        'interval' => 'week'
    ),
    '2' => array(
        'name' => 'Monthly Subscription',
        'price' => 85,
        'interval' => 'month'
    ),
    '3' => array(
        'name' => 'Yearly Subscription',
        'price' => 950,
        'interval' => 'year'
    ),
    '4' => array(
        'name' => 'Daily Subscription',
        'price' => 5,
        'interval' => 'day'
    )
);
$currency = "USD";

function message($type, $data){
    return "<div class='alert alert-{$type} ' id='flashMessage'>{$data}</div>";
}

class DB{
    public const STRIPE_API_KEY = 'xxxxx';

    public const STRIPE_PUBLISHABLE_KEY = 'xxxxx';
    

    private $connection;
    private $stmt;

    public function __construct($dsn, $username,  $password)
    {
        $this->connection = new PDO($dsn, $username, $password);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    // to get the last inserted ID from database. Used in payment.php
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    // Insert
    public function insert($table, $data)
    {
        $placeholders = [];

        foreach ($data as $key => $value) {
            $placeholders[] = ' :' . $key;
        }

        $query = 'INSERT INTO ' . $table . ' (' . implode(',', array_keys($data)) . ') VALUES (' . implode(',', $placeholders) . ')';
        $stmt = $this->connection->prepare($query);

        foreach ($data as $placeholder => $val) {
            $stmt->bindValue(':' . $placeholder, $val);
        }

        return $stmt->execute();
    }

    // select
    public function select($table, $columns = '*', $data = [])
    {
        $query = 'SELECT ' . $columns . ' FROM ' . $table;
        //$query = 'SELECT ' . $columns . ' FROM ' . $table . ' ORDER BY id DESC';

        if (!empty($data)) {
            $string = [];

            foreach ($data as $key => $value) {
                $string[] = "`{$key}` = :{$key}";
            }

            $query .= ' WHERE ' . implode(',', $string);
        }

        $this->stmt = $this->connection->prepare($query);

        foreach ($data as $placeholder => $val) {
            $this->stmt->bindParam(':' . $placeholder, $val);
        }

        return $this->stmt;
    }

    // update
    public function update($table, array $setvals, array $condition)
    {
        try {
            $i = 0;
            foreach ($setvals as $key => $value) {
                $setExp[$i] = $key . "='" . $value . "'";
                $i++;
            }
            $setExp = implode(", ", $setExp);
            $a = 0;
            foreach ($condition as $key => $value) {
                $setCondition[$a] = $key . "='" . $value . "'";
                $a++;
            }
            $setCondition = implode(" AND ", $setCondition);
            $stmt = $this->connection->prepare("UPDATE $table SET $setExp WHERE $setCondition");
            $stmt->execute();
            //echo "1 row updated successfully";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    // delete
    public function delete($table, array $arrayval)
    {
        try {
            $i = 0;
            foreach ($arrayval as $key => $value) {
                $expression[$i] = $key . "='" . $value . "'";
            }
            $expression = implode(" AND ", $expression);
            $stmt = $this->connection->prepare("DELETE FROM $table WHERE $expression");
            $stmt->execute();
            //echo "1 row deleted successfully";
        } catch (Exception $e) {
            echo $e . "<br>" . $e->getMessage();
        }
    }
}

define('DSN', 'mysql:dbname=stripe_plan;host=localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');

$connection = new DB(DSN, DB_USERNAME, DB_PASSWORD);