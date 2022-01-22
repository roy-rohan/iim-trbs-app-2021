<?php

include_once __DIR__ . '/Common/ModelUtils.php';

class ContactRole
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table = 'contact_role';

    // Event Properties
    public $contact_role_id;
    public $designation;
    public $priority;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get InterestedUsers
    public function read($data)
    {
        // Create query
        $query = 'SELECT * FROM ' . $this->table;

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single Event
    public function read_single()
    {
        // Create query
        $query =
            'SELECT * FROM ' . $this->table . ' WHERE contact_role_id = :contact_role_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":contact_role_id", $this->contact_role_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Set properties
        $this->populateModelFields($row, []);
    }

    // Create IntestedUser
    public function create()
    {
        // Create query
        $ignoreList = ["contact_role_id"];
        $query = $this->generateInsertQuery($ignoreList);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $this->bindParams($stmt, $ignoreList);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update Event
    public function update()
    {
        // Create query
        $query = $this->generateUpdateQuery(
            "WHERE contact_role_id = :contact_role_id",
            ["contact_role_id"]
        );
        // Prepare statement
        $stmt = $this->conn->prepare($query);

        $this->bindParams(
            $stmt,
            []
        );

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete Event
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE contact_role_id = :contact_role_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->contact_role_id = htmlspecialchars(strip_tags($this->contact_role_id));

        // Bind data
        $stmt->bindParam(':contact_role_id', $this->contact_role_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
