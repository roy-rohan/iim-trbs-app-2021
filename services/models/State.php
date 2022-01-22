<?php

include_once __DIR__ . '/Common/ModelUtils.php';

class State
{
    // DB stuff
    private $conn;
    private $table = 'state';

    // InterestedUser Properties
    public $state_id;
    public $name;
    public $created_at;
    public $updated_at;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get State
    public function read()
    {
        // Create query
        $query = 'SELECT * FROM ' . $this->table;

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Get Single State
    public function read_single()
    {
        // Create query
        $query =
            'SELECT * FROM ' . $this->table . ' WHERE state_id = :state_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":state_id", $this->state_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->state_id = $row['state_id'];
        $this->name = $row['name'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
        if ($this->state_id)
            return true;
        else
            return false;
    }

    // Create IntestedUser
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET state_id = :state_id,'
            . ' name = :name, created_at = :created_at,'
            . ' updated_at = :updated_at';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->state_id = htmlspecialchars(strip_tags($this->state_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':state_id', $this->state_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);

        return false;
    }

    // Update InterestedUser
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET state_id = :state_id,'
            . ' name = :name,'
            . ' updated_at = :updated_at WHERE state_id = :state_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->state_id = htmlspecialchars(strip_tags($this->state_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':state_id', $this->state_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete State
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE state_id = :state_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->state_id = htmlspecialchars(strip_tags($this->state_id));

        // Bind data
        $stmt->bindParam(':state_id', $this->state_id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
