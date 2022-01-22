<?php

include_once __DIR__ . '/Common/ModelUtils.php';

class AboutPage
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table = 'about_page';

    // Event Properties
    public $about_page_id;
    public $about;
    public $event_desc;
    public $workshop_desc;
    public $speaker_desc;
    public $video_link;

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
            'SELECT * FROM ' . $this->table . ' LIMIT 1';
			
        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Set properties
        // $this->populateModelFields($row, []);
        $this->about_page_id = $row['about_page_id'];
        $this->about = $row['about'];
        $this->event_desc = $row['event_desc'];
        $this->workshop_desc = $row['workshop_desc'];
        $this->speaker_desc = $row['speaker_desc'];
        $this->video_link = $row['video_link'];
    }

    // Create IntestedUser
    public function create()
    {
        // Create query
        $ignoreList = ["about_page_id"];
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
            "WHERE about_page_id = :about_page_id",
            ["about_page_id"]
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
        $query = 'DELETE FROM ' . $this->table . ' WHERE about_page_id = :about_page_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->about_page_id = htmlspecialchars(strip_tags($this->about_page_id));

        // Bind data
        $stmt->bindParam(':about_page_id', $this->about_page_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
