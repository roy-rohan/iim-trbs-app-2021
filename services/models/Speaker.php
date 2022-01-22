<?php

include_once __DIR__ . '/Common/ModelUtils.php';

class Speaker
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table = 'speaker';

    // Speaker Properties
    public $speaker_id;
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
            . 'image i ON e.speaker_id = i.entity_id AND i.entity_type = "speaker" AND i.type = "speaker"';

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single Speaker
    public function read_single()
    {
        // Create query
        $query =
            'SELECT e.*, i.path as image_url FROM ' . $this->table . ' e LEFT JOIN '
            . 'image i ON e.speaker_id = i.entity_id AND i.entity_type = "speaker" AND i.type = "speaker"' . ' WHERE speaker_id = :speaker_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":speaker_id", $this->speaker_id);

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
        $ignoreList = ["image_url", "speaker_id"];
        $query = $this->generateInsertQuery($ignoreList);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
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

    // Update Speaker
    public function update()
    {
        // Create query
        $query = $this->generateUpdateQuery(
            "WHERE speaker_id = :speaker_id",
            ["image_url", "speaker_id", "created_at"]
        );
        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
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

    // Delete Speaker
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE speaker_id = :speaker_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->speaker_id = htmlspecialchars(strip_tags($this->speaker_id));

        // Bind data
        $stmt->bindParam(':speaker_id', $this->speaker_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
