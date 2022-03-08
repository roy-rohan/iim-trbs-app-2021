<?php

include_once __DIR__ . "/Common/ModelUtils.php";

class UserCertificate
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table;

    // UserCertificate Properties
    public $app_user_id;
    public $certificate_id;
    public $name;
    public $image_id;
    public $image_url;
    public $content;
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
        $this->table = TABLES::$USER_CERTIFICATE;
    }

    // Get UserCertificates
    public function getUsersByCertificateId($certificate_id)
    {
        // Create query
        $query = 'SELECT a.* FROM ' . TABLES::$APP_USER . ' a INNER JOIN ' . $this->table . ' uc '
                . ' WHERE a.app_user_id = uc.user_id AND uc.certificate_id = :certificate_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":certificate_id", $certificate_id);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get UserCertificates
    public function getCertificatesByUserId($user_id)
    {
        // Create query
        $query = 'SELECT c.*, i.path as image_url FROM ' . TABLES::$CERTIFICATE . ' c LEFT JOIN ' 
        .'image i ON c.certificate_id = i.entity_id AND i.entity_type = "certificate" AND i.type = "certificate" '
        .'INNER JOIN ' . $this->table . ' uc '
            . ' WHERE c.certificate_id = uc.certificate_id AND uc.user_id = :user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":user_id", $user_id);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Create IntestedUser
    public function create($user_id, $certificate_id)
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET user_id = :user_id, certificate_id = :certificate_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":certificate_id", $certificate_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Update UserCertificate
    public function update($user_id, $certificate_id)
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET user_id = :user_id, certificate_id = :certificate_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":certificate_id", $certificate_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete UserCertificate
    public function deleteByCertificateId($certificate_id)
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE certificate_id = :certificate_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(':certificate_id', $certificate_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete UserCertificate
    public function deleteByUserId($user_id)
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE user_id = :user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(':user_id', $user_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
