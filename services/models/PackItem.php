<?php

include_once __DIR__ . '/Common/ModelUtils.php';

class PackItem
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table = 'pack_item';

    // Event Properties
    public $pack_item_id;
    public $product_name;
    public $product_type;
    public $product_image;
    public $product_id;
    public $price;
    public $slug;
    public $pack_id;

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
            $query = 'SELECT * FROM ' . $this->table .
            ' WHERE pack_item_id = :pack_item_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":pack_item_id", $this->pack_item_id);

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
        $ignoreList = ["pack_item_id"];
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
            "WHERE pack_item_id = :pack_item_id",
            ["pack_item_id", "created_at"]
        );
        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

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
        $query = 'DELETE FROM ' . $this->table . ' WHERE pack_item_id = :pack_item_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->pack_item_id = htmlspecialchars(strip_tags($this->pack_item_id));

        // Bind data
        $stmt->bindParam(':pack_item_id', $this->pack_item_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
