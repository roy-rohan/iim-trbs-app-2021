<?php

include_once __DIR__ . '/Common/ModelUtils.php';

class Sponser
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table = 'sponser';

    // Sponser Properties
    public $sponser_id;
    public $title;
    public $type;
    public $image_url;
    public $link;
    public $size;
	public $visible;
    public $view_order;
    public $show_in_home_page;
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
            . 'image i ON e.sponser_id = i.entity_id AND i.entity_type = "sponser" AND i.type = "sponser"';

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single Sponser
    public function read_single()
    {
        // Create query
        $query =
            'SELECT e.*, i.path as image_url FROM ' . $this->table . ' e LEFT JOIN '
            . 'image i ON e.sponser_id = i.entity_id AND i.entity_type = "sponser" AND i.type = "sponser"' . ' WHERE sponser_id = :sponser_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":sponser_id", $this->sponser_id);

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
        $ignoreList = ["image_url", "sponser_id"];
        $query = $this->generateInsertQuery($ignoreList);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->type = htmlspecialchars(strip_tags($this->type));
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

    // Update Sponser
    public function update()
    {
        // Create query
        $query = $this->generateUpdateQuery(
            "WHERE sponser_id = :sponser_id",
            ["image_url", "sponser_id", "created_at"]
        );
        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->view_order = htmlspecialchars(strip_tags($this->view_order));
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

    // Delete Sponser
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE sponser_id = :sponser_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->sponser_id = htmlspecialchars(strip_tags($this->sponser_id));

        // Bind data
        $stmt->bindParam(':sponser_id', $this->sponser_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    public function getCategories()
    {
        $query = 'SELECT DISTINCT type FROM ' . $this->table;

        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }
}
