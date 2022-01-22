<?php

include_once __DIR__ . "/Common/ModelUtils.php";

class Strategizer
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table;

    // PaymentDetail Properties
    public $leaderboard_id;
    public $type;
    public $name;
    public $college_id;
    public $college;
    public $score;
    public $created_at;
    public $updated_at;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = TABLES::$STRATEGIZER;
    }

    // Get PaymentDetails
    public function read($data)
    {
        // Create query
        $query = 'SELECT s.*, c.name as college FROM ' . $this->table . ' s LEFT JOIN '
            . 'college c on s.college_id = c.college_id';

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single PaymentDetail
    public function read_single()
    {
        // Create query
        $query = 'SELECT s.*, c.name as college FROM ' . $this->table . ' s LEFT JOIN '
            . 'college c on s.college_id = c.college_id' . ' WHERE leaderboard_id = :leaderboard_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":leaderboard_id", $this->leaderboard_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->leaderboard_id = $row['leaderboard_id'];
        $this->type = $row['type'];
        $this->name = $row['name'];
        $this->college = $row['college'];
        $this->college_id = $row['college_id'];
        $this->score = $row['score'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    // Create IntestedUser
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET type = :type, college_id = :college_id, '
            . ' name = :name, score = :score, created_at = :created_at,'
            . ' updated_at = :updated_at';


        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->college_id = htmlspecialchars(strip_tags($this->college_id));
        $this->score = htmlspecialchars(strip_tags($this->score));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':college_id', $this->college_id);
        $stmt->bindParam(':score', $this->score);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update PaymentDetail
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table .  ' SET type = :type, college_id = :college_id, '
            . ' score = :score, name = :name,' .
            'updated_at = :updated_at' .
            ' WHERE leaderboard_id = :leaderboard_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->college_id = htmlspecialchars(strip_tags($this->college_id));
        $this->score = htmlspecialchars(strip_tags($this->score));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(
            ':leaderboard_id',
            $this->leaderboard_id
        );
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':college_id', $this->college_id);
        $stmt->bindParam(':score', $this->score);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete PaymentDetail
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE leaderboard_id = :leaderboard_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->leaderboard_id = htmlspecialchars(strip_tags($this->leaderboard_id));

        // Bind data
        $stmt->bindParam(':leaderboard_id', $this->leaderboard_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
