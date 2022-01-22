<?php
include_once __DIR__ . "/../Common/ModelUtils.php";

class CouponMaster
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table;

    // coupon variables
    public $coupon_id;
    public $coupon_code;
    public $status;
    public $discount;
    public $user_id;
    public $applied_on;
    public $created_at;
    public $updated_at;



    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = TABLES::$COUPON_MASTER;
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

    // Get Single CouponMater
    public function read_single()
    {
        // Create query
        $query = 'SELECT * FROM ' . $this->table . ' WHERE coupon_id = :coupon_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":coupon_id", $this->coupon_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        if (!$row['coupon_id']) {
            return false;
        } else {
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

    // Create CouponMater
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET coupon_code = :coupon_code, '
            . ' discount = :discount, status = :status, created_at = :created_at, '
            . ' updated_at = :updated_at';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->coupon_code = $this->generateCouponCode(8);

        // Bind data
        $stmt->bindParam(':coupon_code', $this->coupon_code);
        $stmt->bindParam(':discount', $this->discount);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update CouponMater
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET'
            . ' discount = :discount, status = :status, applied_on = :applied_on, '
            . ' updated_at = :updated_at, user_id = :user_id WHERE coupon_id = :coupon_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->applied_on = htmlspecialchars(strip_tags($this->applied_on));
        // Bind data
        $stmt->bindParam(
            ':coupon_id',
            $this->coupon_id
        );
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':discount', $this->discount);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':applied_on', $this->applied_on);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete CouponMater
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE coupon_id = :coupon_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->coupon_id = htmlspecialchars(strip_tags($this->coupon_id));

        // Bind data
        $stmt->bindParam(':coupon_id', $this->coupon_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    private function generateCouponCode($length)
    {
        $random_string = '';
        while (true) {
            for ($i = 0; $i < $length; $i++) {
                $number = random_int(1, 36);
                $character = base_convert($number, 10, 36);
                $random_string .= $character;
            }
            $random_string = strtoupper($random_string);
            if (strlen($random_string) > 8) {
                $random_string = substr($random_string, 0, 8);
            } else if (strlen($random_string) < 8) {
                $random_string = str_pad($random_string, 8, "0");
            }

            $query = 'SELECT coupon_code FROM ' . $this->table . ' WHERE coupon_code = :coupon_code';

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':coupon_code', $random_string);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row['coupon_code']) {
                break;
            }
            $random_string = '';
        }

        return $random_string;
    }
	
	 public function invalidatePreviousYearCoupons()
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET status = -1 WHERE year(created_at) != year(sysdate())';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
	
}
