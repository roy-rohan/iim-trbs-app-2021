<?php

include_once __DIR__ . "/../../Common/ModelUtils.php";

class Order
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table;

    // Order Properties
    public $order_id;
    public $user_id;
    public $total;
    public $discount;
    public $coupon_id;
    public $taxes;
    public $status;
    public $created_at;
    public $updated_at;

    public $payment_id;
    public $transaction_id;
    public $mode;
    public $amount;
    public $description;
    public $payment_status;
    public $payment_created_at;
    public $payment_updated_at;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = TABLES::$ORDER;
    }

    // Get InterestedUsers
    public function read($data)
    {
        // Create query
        $query = 'SELECT o.*, pd.payment_id, pd.transaction_id, pd.amount, pd.mode,'
            . ' pd.status as payment_status, pd.created_at as payment_created_at,'
            . ' pd.updated_at as payment_updated_at, pd.description FROM '
            . $this->table . ' o LEFT JOIN payment_detail pd '
            . ' on o.order_id = pd.order_id';

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single Order
    public function read_single()
    {
        // Create query
        $query = 'SELECT o.*, pd.payment_id, pd.transaction_id, pd.amount, pd.mode,'
            . ' pd.status as payment_status, pd.created_at as payment_created_at,'
            . ' pd.updated_at as payment_updated_at, pd.description FROM '
            . $this->table . ' o LEFT JOIN payment_detail pd '
            . ' on o.order_id = pd.order_id WHERE o.order_id = :order_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":order_id", $this->order_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row['order_id']) {
            return false;
        } else {
            // Set properties
            $this->order_id = $row['order_id'];
            $this->user_id = $row['user_id'];
            $this->total = $row['total'];
            $this->discount = $row['discount'];
            $this->coupon_id = $row['coupon_id'];
            $this->taxes = $row['taxes'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];

            $this->payment_id = $row['payment_id'];
            $this->transaction_id = $row['transaction_id'];
            $this->amount = $row['amount'];
            $this->mode = $row['mode'];
            $this->payment_status = $row['payment_status'];
            $this->description = $row['description'];
            $this->payment_created_at = $row['payment_created_at'];
            $this->payment_updated_at = $row['payment_updated_at'];
            return true;
        }
    }

    // Create Order
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET total = :total, user_id = :user_id,'
            . ' discount = :discount, taxes = :taxes, status = :status, coupon_id = :coupon_id,'
            . ' created_at = :created_at, updated_at = :updated_at';


        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->total = htmlspecialchars(strip_tags($this->total));
        $this->discount = htmlspecialchars(strip_tags($this->discount));
        $this->taxes = htmlspecialchars(strip_tags($this->taxes));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':coupon_id', $this->coupon_id);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':discount', $this->discount);
        $stmt->bindParam(':taxes', $this->taxes);
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

    // Update Order
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table .  ' SET total = :total, user_id = :user_id,'
            . ' discount = :discount, taxes = :taxes, status = :status, coupon_id = :coupon_id,'
            . ' updated_at = :updated_at WHERE order_id = :order_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->order_id = htmlspecialchars(strip_tags($this->order_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->total = htmlspecialchars(strip_tags($this->total));
        $this->discount = htmlspecialchars(strip_tags($this->discount));
        $this->taxes = htmlspecialchars(strip_tags($this->taxes));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':coupon_id', $this->coupon_id);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':discount', $this->discount);
        $stmt->bindParam(':taxes', $this->taxes);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete Order
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE order_id = :order_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->order_id = htmlspecialchars(strip_tags($this->order_id));

        // Bind data
        $stmt->bindParam(':order_id', $this->order_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
