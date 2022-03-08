<?php

include_once __DIR__ . "/../Common/ModelUtils.php";

class AppUser
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table;

    // AppUser Properties
    public $app_user_id;
    public $first_name;
    public $last_name;
    public $email_id;
    public $mobile_no;
    public $password;
    public $college_id;
    public $college;
    public $year;
    public $address;
    public $email_validated;
    public $state_id;
    public $state;
    public $profile_image_id;
    public $profile_image;
    public $role;
    public $login_id;
    public $cart_id;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = TABLES::$APP_USER;
    }

    // Get AppUsers
    public function read($data)
    {
        // Create query
        $query = 'SELECT u.*, i.path as profile_image, c.name as college, s.name as state FROM ' . $this->table . ' u LEFT JOIN '
            . 'image i ON u.app_user_id = i.entity_id AND i.entity_type = "user" LEFT JOIN college c on '
            . 'u.college_id = c.college_id LEFT JOIN state s on '
            . 'u.state_id = s.state_id';

        $query = $this->processFiltersAndOrderBy($query, $data);
        $customSortQuery = "u.app_user_id desc";

        if (strpos(
            $query,
            ' ORDER BY '
        )) {
            $query .= ', ' . $customSortQuery;
        } else {
            $query .= ' ORDER BY ' . $customSortQuery;
        }

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single AppUser
    public function read_single()
    {
        // Create query
        $query = 'SELECT u.*, i.path as profile_image, c.name as college, s.name as state FROM ' . $this->table . ' u LEFT JOIN '
            . 'image i ON u.app_user_id = i.entity_id AND i.entity_type = "user" LEFT JOIN college c on '
            . 'u.college_id = c.college_id LEFT JOIN state s on '
            . 'u.state_id = s.state_id  WHERE app_user_id = :app_user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":app_user_id", $this->app_user_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->app_user_id = $row['app_user_id'];
        $this->first_name = $row['first_name'];
        $this->last_name = $row['last_name'];
        $this->email_id = $row['email_id'];
        $this->mobile_no = $row['mobile_no'];
        $this->college = $row['college'];
        $this->year = $row['year'];
        $this->address = $row['address'];
        $this->email_validated = $row['email_validated'];
        $this->profile_image = $row['profile_image'];
        $this->state = $row['state'];
        $this->role = $row['role'];
        $this->login_id = $row['login_id'];
        $this->cart_id = $row['cart_id'];
        $this->created_by = $row['created_by'];
        $this->updated_by = $row['updated_by'];
        $this->is_active = $row['is_active'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    // Create IntestedUser
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET first_name = :first_name, last_name = :last_name, '
            . ' email_id = :email_id, mobile_no = :mobile_no, college_id = :college_id,'
            . ' year = :year, password = :password, email_validated = :email_validated, '
            . ' address = :address, state_id = :state_id, role = :role,'
            . ' login_id = :login_id, cart_id = :cart_id, created_by = :created_by, updated_by = :updated_by,'
            . ' is_active = :is_active, created_at = :created_at,'
            . ' updated_at = :updated_at';


        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email_id = htmlspecialchars(strip_tags($this->email_id));
        $this->mobile_no = htmlspecialchars(strip_tags($this->mobile_no));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->email_validated = htmlspecialchars(strip_tags($this->email_validated));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->login_id = htmlspecialchars(strip_tags($this->login_id));
        $this->cart_id = htmlspecialchars(strip_tags($this->cart_id));
        $this->created_by = 1;
        $this->updated_by = 1;
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email_id', $this->email_id);
        $stmt->bindParam(':mobile_no', $this->mobile_no);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':college_id', $this->college_id);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':email_validated', $this->email_validated);
        $stmt->bindParam(':state_id', $this->state_id);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':login_id', $this->login_id);
        $stmt->bindParam(':cart_id', $this->cart_id);
        $stmt->bindParam(':created_by', $this->created_by);
        $stmt->bindParam(':updated_by', $this->updated_by);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update AppUser
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table .  ' SET first_name = :first_name, last_name = :last_name, '
            . ' email_id = :email_id, mobile_no = :mobile_no, college_id = :college_id,'
            . ' year = :year,'
            . ' address = :address, state_id = :state_id, role = :role,'
            . ' login_id = :login_id, updated_by = :updated_by,'
            . ' is_active = :is_active, '
            . ' updated_at = :updated_at WHERE app_user_id = :app_user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email_id = htmlspecialchars(strip_tags($this->email_id));
        $this->mobile_no = htmlspecialchars(strip_tags($this->mobile_no));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->college_id = htmlspecialchars(strip_tags($this->college_id));
        $this->state_id = htmlspecialchars(strip_tags($this->state_id));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->login_id = htmlspecialchars(strip_tags($this->login_id));
        $this->updated_by = htmlspecialchars(strip_tags($this->updated_by));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(
            ':app_user_id',
            $this->app_user_id
        );
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email_id', $this->email_id);
        $stmt->bindParam(':mobile_no', $this->mobile_no);
        $stmt->bindParam(':college_id', $this->college_id);
        $stmt->bindParam(
            ':year',
            $this->year
        );
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':state_id', $this->state_id);
        $stmt->bindParam(
            ':role',
            $this->role
        );
        $stmt->bindParam(':login_id', $this->login_id);
        $stmt->bindParam(':updated_by', $this->updated_by);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete AppUser
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE app_user_id = :app_user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->app_user_id = htmlspecialchars(strip_tags($this->app_user_id));

        // Bind data
        $stmt->bindParam(':app_user_id', $this->app_user_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Check if App User is present
    public function checkAppUser()
    {
        // Create query
        $query = 'SELECT *  FROM ' . $this->table . ' WHERE email_id = :email_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->email_id = htmlspecialchars(strip_tags($this->email_id));

        // Bind data
        $stmt->bindParam(':email_id', $this->email_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($row)) {
            return true;
        }
        return false;
    }

    public function activateUser()
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET email_validated = :email_validated'
            . ' WHERE app_user_id = :app_user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $activationFlag = 1;
        $stmt->bindParam(':email_validated', $activationFlag);
        $stmt->bindParam(':app_user_id', $this->app_user_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    function verifyPassword()
    {
        // Create query
        $query = 'SELECT password FROM ' . $this->table . ' WHERE app_user_id = :app_user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(':app_user_id', $this->app_user_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($this->password, $row['password'])) {
                    return true;
                }
            }
        }

        return false;
    }

    function changePassword()
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET password = :password'
            . ' WHERE app_user_id = :app_user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':app_user_id', $this->app_user_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    function changePasswordByEmail()
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET password = :password'
            . ' WHERE email_id = :email_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email_id', $this->email_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
