<?php

include_once __DIR__ . '/Common/ModelUtils.php';

class Member
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table = 'member';

    // Member Properties
    public $member_id;
    public $name;
    public $designation;
    public $more_info;
    public $visible;
    public $created_at;
    public $updated_at;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get InterestedUsers
    public function read($data)
    {
        // Create query
        $query = 'SELECT e.*, i.path as image_url FROM ' . $this->table . ' e LEFT JOIN '
            . 'image i ON e.member_id = i.entity_id AND i.entity_type = "member" AND i.type = "member"';

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single Member
    public function read_single()
    {
        // Create query
        $query =
            'SELECT e.*, i.path as image_url FROM ' . $this->table . ' e LEFT JOIN '
            . 'image i ON e.member_id = i.entity_id AND i.entity_type = "member" AND i.type = "member"' . ' WHERE member_id = :member_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":member_id", $this->member_id);

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
        $ignoreList = ["image_url", "member_id"];
        $query = $this->generateInsertQuery($ignoreList);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->designation = htmlspecialchars(strip_tags($this->designation));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $this->bindParams($stmt, $ignoreList);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update Member
    public function update()
    {
        // Create query
        $query = $this->generateUpdateQuery(
            "WHERE member_id = :member_id",
            ["image_url", "member_id", "created_at"]
        );
        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->designation = htmlspecialchars(strip_tags($this->designation));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        $this->bindParams(
            $stmt,
            ["image_url", "created_at"]
        );

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete Member
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE member_id = :member_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->member_id = htmlspecialchars(strip_tags($this->member_id));

        // Bind data
        $stmt->bindParam(':member_id', $this->member_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
