<?php

include_once __DIR__ . '/Common/ModelUtils.php';

class Connextion
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table = 'connexion';

    // Connextion Properties
    public $connexion_id;
    public $name;
    public $introduction;
    public $slug;
    public $image_url;
    public $designation;
    public $topic;
    public $duration;
    public $date;
    public $time;
    public $biography;
    public $registration;
    public $venue;
    public $view_order;
    public $price;
    public $type;
    public $is_active;
	public $visible;
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
            . 'image i ON e.connexion_id = i.entity_id AND i.entity_type = "connexion" AND i.type = "connexion"';

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single Connextion
    public function read_single()
    {
        // Create query
        $query =
            'SELECT e.*, i.path as image_url FROM ' . $this->table . ' e LEFT JOIN '
            . 'image i ON e.connexion_id = i.entity_id AND i.entity_type = "connexion" AND i.type = "connexion"' . ' WHERE connexion_id = :connexion_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":connexion_id", $this->connexion_id);

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
        $ignoreList = ["image_url", "connexion_id"];
        $query = $this->generateInsertQuery($ignoreList);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->introduction = htmlspecialchars(strip_tags($this->introduction));
        $this->slug = htmlspecialchars(strip_tags($this->slug));
        $this->designation = htmlspecialchars(strip_tags($this->designation));
        $this->biography = htmlspecialchars(strip_tags($this->biography));
        $this->registration = htmlspecialchars(strip_tags($this->registration));
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

    // Update Connextion
    public function update()
    {
        // Create query
        $query = $this->generateUpdateQuery(
            "WHERE connexion_id = :connexion_id",
            ["image_url", "connexion_id", "created_at"]
        );
        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->introduction = htmlspecialchars(strip_tags($this->introduction));
        $this->slug = htmlspecialchars(strip_tags($this->slug));
        $this->designation = htmlspecialchars(strip_tags($this->designation));
        $this->biography = htmlspecialchars(strip_tags($this->biography));
        $this->registration = htmlspecialchars(strip_tags($this->registration));
        $this->view_order = htmlspecialchars(strip_tags($this->view_order));
        $this->is_active = htmlspecialchars(strip_tags($this->is_active));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        $this->bindParams(
            $stmt,
            ["image_url", "image_path", "created_at"]
        );

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete Connextion
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE connexion_id = :connexion_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->connexion_id = htmlspecialchars(strip_tags($this->connexion_id));

        // Bind data
        $stmt->bindParam(':connexion_id', $this->connexion_id);

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
