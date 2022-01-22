<?php

include_once __DIR__ . "/../../Common/ModelUtils.php";

class Cart
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table;

    // Cart Properties
    public $cart_id;
    public $total;
    public $sub_total;
    public $discount;

    // coupon variables
    public $coupon_id;
    public $coupon_code;
    public $status;
    public $user_id;
    public $applied_on;
    public $created_at;
    public $updated_at;



    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = TABLES::$CART;
    }

    // Get InterestedUsers
    public function read($data)
    {
        // Create query
        $query = 'SELECT ca.cart_id, ca.total, cm.* FROM ' . $this->table . ' ca LEFT JOIN coupon_master cm ' .
            ' on ca.coupon_id = cm.coupon_id';
        $query = $this->processFiltersAndOrderBy($query, $data);
        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single Cart
    public function read_single()
    {
        // Create query
        $query = 'SELECT ca.cart_id, ca.total, ca.sub_total, cm.* FROM ' . $this->table . ' ca LEFT JOIN coupon_master cm ' .
            ' on ca.coupon_id = cm.coupon_id WHERE cart_id = :cart_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":cart_id", $this->cart_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        if (!$row['cart_id']) {
            return false;
        } else {
            $this->cart_id = $row['cart_id'];
            $this->total = $row['total'];
            $this->sub_total = $row['sub_total'];
            $this->coupon_id = $row['coupon_id'];
            $this->coupon_code = $row['coupon_code'];
            $this->status = $row['status'];
            $this->discount = $row['discount'];
            $this->applied_on = $row['applied_on'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
    }

    // Create Cart
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET total = :total, sub_total = :sub_total, ' . 'discount = :discount';


        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->total = htmlspecialchars(strip_tags($this->total));
        // Bind data
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':sub_total', $this->sub_total);
        $stmt->bindParam(':discount', $this->discount);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update Cart
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table .  ' SET total = :total, sub_total = :sub_total, discount = :discount, coupon_id = :coupon_id WHERE cart_id = :cart_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->total = htmlspecialchars(strip_tags($this->total));
        // Bind data
        $stmt->bindParam(
            ':cart_id',
            $this->cart_id
        );
        $stmt->bindParam(
            ':total',
            $this->total
        );
        $stmt->bindParam(
            ':sub_total',
            $this->sub_total
        );
        $stmt->bindParam(
            ':discount',
            $this->discount
        );
        $stmt->bindParam(
            ':coupon_id',
            $this->coupon_id
        );

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete Cart
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE cart_id = :cart_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->cart_id = htmlspecialchars(strip_tags($this->cart_id));

        // Bind data
        $stmt->bindParam(':cart_id', $this->cart_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
