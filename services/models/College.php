<?php

include_once __DIR__ . "/Common/ModelUtils.php";

class College
{
    use QueryUtils;

    // DB stuff
    private $conn;
    private $table = 'college';

    // InterestedUser Properties
    public $college_id;
    public $name;
    public $is_active;
    public $created_at;
    public $updated_at;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getCount($data)
    {
        $query =
            'SELECT count(*) as row_count FROM ' . $this->table;

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row["row_count"];
    }

    // Get College
    public function read($data, $offset)
    {
        // Create query
        $query = 'SELECT * FROM ' . $this->table;

        $query = $this->processFiltersAndOrderBy($query, $data);

        $query .= " LIMIT " . $offset . ", 300";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single College
    public function read_single()
    {
        // Create query
        $query =
            'SELECT * FROM ' . $this->table . ' WHERE college_id = :college_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":college_id", $this->college_id);
        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->college_id = $row['college_id'];
        $this->name = $row['name'];
        $this->is_active = $row['is_active'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
        if ($this->college_id)
            return true;
        else
            return false;
    }

    // Create IntestedUser
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET '
            . ' name = :name, is_active = :is_active, created_at = :created_at,'
            . ' updated_at = :updated_at';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Update InterestedUser
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET '
            . ' name = :name, is_active = :is_active,'
            . ' updated_at = :updated_at WHERE college_id = :college_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
		$stmt->bindParam(':college_id', $this->college_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete College
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE college_id = :college_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->college_id = htmlspecialchars(strip_tags($this->college_id));

        // Bind data
        $stmt->bindParam(':college_id', $this->college_id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
