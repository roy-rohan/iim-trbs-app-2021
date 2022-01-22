<?php

include_once __DIR__ . '/Common/ModelUtils.php';

class InterestedUser
{
    use QueryUtils;

    // DB stuff
    private $conn;
    private $table = 'interested_user';

    // InterestedUser Properties
    public $interested_user_id;
    public $first_name;
    public $last_name;
    public $email_id;
    public $mobile_no;
    public $college;
    public $college_id;
    public $state;
    public $state_id;
    public $event_name;
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
        $query = 'SELECT u.interested_user_id, u.first_name, u.last_name, u.email_id, u.mobile_no, 
                  u.mobile_no, u.event_name, c.name as college, s.name as state, u.created_at,
                 u.updated_at FROM ' . $this->table . ' u LEFT JOIN college c '
            . 'on u.college_id = c.college_id LEFT JOIN state s on '
            . 'u.state_id = s.state_id';

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single InterestedUser
    public function read_single()
    {
        // Create query
        $query =
            'SELECT u.interested_user_id, u.first_name, u.last_name, u.email_id, u.mobile_no, 
                  u.college_id, u.event_name, c.name as college, s.state_id, s.name as state, u.created_at,
                 u.updated_at FROM ' . $this->table . ' u LEFT JOIN college c '
            . 'on u.college_id = c.college_id LEFT JOIN state s on '
            . 'u.state_id = s.state_id WHERE u.interested_user_id = :interested_user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":interested_user_id", $this->interested_user_id);

        // Execute query
        $this->executeQuery($stmt);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->interested_user_id = $row['interested_user_id'];
        $this->first_name = $row['first_name'];
        $this->last_name = $row['last_name'];
        $this->email_id = $row['email_id'];
        $this->mobile_no = $row['mobile_no'];
        $this->college = $row['college'];
        $this->college_id = $row['college_id'];
        $this->state_id = $row['state_id'];
        $this->state = $row['state'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    // Create IntestedUser
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET first_name = :first_name,'
            . ' last_name = :last_name, email_id = :email_id, mobile_no = :mobile_no,'
            . ' college_id = :college_id, state_id = :state_id, event_name = :event_name, created_at = :created_at,'
            . ' updated_at = :updated_at';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email_id = htmlspecialchars(strip_tags($this->email_id));
        $this->mobile_no = htmlspecialchars(strip_tags($this->mobile_no));
        $this->college_id = htmlspecialchars(strip_tags($this->college_id));
        $this->state_id = htmlspecialchars(strip_tags($this->state_id));
        $this->event_name = htmlspecialchars(strip_tags($this->event_name));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email_id', $this->email_id);
        $stmt->bindParam(':mobile_no', $this->mobile_no);
        $stmt->bindParam(':college_id', $this->college_id);
        $stmt->bindParam(':state_id', $this->state_id);
        $stmt->bindParam(':event_name', $this->event_name);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update InterestedUser
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET first_name = :first_name,'
            . ' last_name = :last_name, email_id = :email_id, mobile_no = :mobile_no,'
            . ' college_id = :college_id, state_id = :state_id, event_name = :event_name,'
            . ' updated_at = :updated_at WHERE interested_user_id = :interested_user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email_id = htmlspecialchars(strip_tags($this->email_id));
        $this->mobile_no = htmlspecialchars(strip_tags($this->mobile_no));
        $this->college_id = htmlspecialchars(strip_tags($this->college_id));
        $this->state_id = htmlspecialchars(strip_tags($this->state_id));
        $this->event_name = htmlspecialchars(strip_tags($this->event_name));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(
            ':interested_user_id',
            $this->interested_user_id
        );
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email_id', $this->email_id);
        $stmt->bindParam(':mobile_no', $this->mobile_no);
        $stmt->bindParam(':college_id', $this->college_id);
        $stmt->bindParam(':state_id', $this->state_id);
        $stmt->bindParam(':event_name', $this->event_name);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }
        return false;
    }

    // Delete InterestedUser
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE interested_user_id = :interested_user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->interested_user_id = htmlspecialchars(strip_tags($this->interested_user_id));

        // Bind data
        $stmt->bindParam(':interested_user_id', $this->interested_user_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }
        return false;
    }
}
