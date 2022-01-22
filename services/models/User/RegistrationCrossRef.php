<?php

include_once __DIR__ . "/../Common/ModelUtils.php";

class OrderItem
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table = TABLES::$REGISTRATION_CROSS_REF;

    // OrderItem Properties
    public $user_id;
    public $product_id;
    public $product_type;
    public $payment_id;
    public $status;
    public $created_at;
    public $updated_at;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get OrderItem
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

    // Create OrderItem
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET user_id = :user_id, payment_id = :payment_id, product_id = :product_id,'
            . ' quantity = :quantity, status = :status, created_at = :created_at, product_type = :product_type,'
            . ' updated_at = :updated_at';


        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->payment_id = htmlspecialchars(strip_tags($this->payment_id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->product_type = htmlspecialchars(strip_tags($this->product_type));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(
            ':quantity',
            $this->quantity
        );
        $stmt->bindParam(':payment_id', $this->payment_id);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(
            ':created_at',
            $this->created_at
        );
        $stmt->bindParam(':product_type', $this->product_type);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update OrderItem
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table .  ' SET user_id = :user_id, payment_id = :payment_id, product_id = :product_id,'
            . ' quantity = :quantity, status = :status, created_at = :created_at, product_type = :product_type,'
            . ' updated_at = :updated_at WHERE user_id = :user_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->payment_id = htmlspecialchars(strip_tags($this->payment_id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->product_type = htmlspecialchars(strip_tags($this->product_type));

        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(
            ':quantity',
            $this->quantity
        );
        $stmt->bindParam(
            ':payment_id',
            $this->payment_id
        );
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':product_type', $this->product_type);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete OrderItem
    public function delete($data)
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table;
        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
