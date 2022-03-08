<?php

include_once __DIR__ . "/Common/ModelUtils.php";

class Certificate
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table;

    // Certificate Properties
    public $certificate_id;
    public $name;
    public $image_url;
    public $content;
    public $content_background_color;
    public $content_position_absolute;
    public $content_position_x;
    public $content_position_y;
    public $visible;
    public $created_at;
    public $updated_at;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = TABLES::$CERTIFICATE;
    }

    // Get Certificates
    public function read($data)
    {
        // Create query
        $query = 'SELECT c.*, i.path as image_url FROM ' . $this->table . ' c LEFT JOIN '
        . 'image i ON c.certificate_id = i.entity_id AND i.entity_type = "certificate" AND i.type = "certificate"';

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single Certificate
    public function read_single()
    {
        // Create query
        $query = 'SELECT c.*, i.path as image_url FROM ' . $this->table . ' c LEFT JOIN '
        . 'image i ON c.certificate_id = i.entity_id AND i.entity_type = "certificate" AND i.type = "certificate" '
        . 'WHERE c.certificate_id = :certificate_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":certificate_id", $this->certificate_id);

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
        $query = 'INSERT INTO ' . $this->table . ' SET content = :content, '
            . ' name = :name, content_background_color = :content_background_color, content_position_absolute = :content_position_absolute, content_position_x = :content_position_x,'
            . ' content_position_y = :content_position_y, visible = :visible,'
            . ' created_at = :created_at, updated_at = :updated_at';


        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->content_position_absolute = htmlspecialchars(strip_tags($this->content_position_absolute));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $ignoreList = ["image_url", "certificate_id"];
        $this->bindParams($stmt,
            $ignoreList
        );

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update Certificate
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET content = :content, '
        . ' name = :name, content_background_color = :content_background_color, content_position_absolute = :content_position_absolute, content_position_x = :content_position_x,'
        . ' content_position_y = :content_position_y, visible = :visible,'
        . ' created_at = :created_at, updated_at = :updated_at'
        . ' WHERE certificate_id = :certificate_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->content_position_absolute = htmlspecialchars(strip_tags($this->content_position_absolute));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $ignoreList = ["image_url"];
        $this->bindParams(
            $stmt,
            $ignoreList
        );

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete Certificate
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE certificate_id = :certificate_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->certificate_id = htmlspecialchars(strip_tags($this->certificate_id));

        // Bind data
        $stmt->bindParam(':certificate_id', $this->certificate_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
