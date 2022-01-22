<?php

include_once __DIR__ . "/Common/ModelUtils.php";

class PaymentDetail
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table;

    // PaymentDetail Properties
    public $payment_id;
    public $transaction_id;
    public $mode;
    public $amount;
    public $user_id;
    public $order_id;
    public $status;
    public $description;
    public $created_at;
    public $updated_at;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = TABLES::$PAYMENT_DETAIL;
    }

    // Get PaymentDetails
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
	
	// Get PaymentDetails
    public function readPaymentsAndUsers($data)
    {
        // Create query
        $query = 'SELECT pd.*, au.app_user_id, au.first_name, au.last_name, au.email_id, au.mobile_no FROM ' . $this->table . ' pd LEFT JOIN '
            . 'app_user au on pd.user_id = au.app_user_id order by pd.payment_id desc';

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }

    // Get Single PaymentDetail
    public function read_single()
    {
        // Create query
        $query = 'SELECT * FROM ' . $this->table . ' WHERE payment_id = :payment_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":payment_id", $this->payment_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->payment_id = $row['payment_id'];
        $this->transaction_id = $row['transaction_id'];
        $this->mode = $row['mode'];
        $this->amount = $row['amount'];
        $this->user_id = $row['user_id'];
        $this->order_id = $row['order_id'];
        $this->status = $row['status'];
        $this->description = $row['description'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    // Create IntestedUser
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET transaction_id = :transaction_id, amount = :amount, '
            . ' mode = :mode, user_id = :user_id, status = :status, created_at = :created_at,'
            . ' updated_at = :updated_at, description = :description, order_id = :order_id';


        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->transaction_id = htmlspecialchars(strip_tags($this->transaction_id));
        $this->mode = htmlspecialchars(strip_tags($this->mode));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->order_id = htmlspecialchars(strip_tags($this->order_id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':transaction_id', $this->transaction_id);
        $stmt->bindParam(':mode', $this->mode);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update PaymentDetail
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table .  ' SET transaction_id = :transaction_id, amount = :amount, '
            . ' user_id = :user_id, status = :status, mode = :mode,' .
            'description = :description, updated_at = :updated_at' .
            ' WHERE payment_id = :payment_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->transaction_id = htmlspecialchars(strip_tags($this->transaction_id));
        $this->mode = htmlspecialchars(strip_tags($this->mode));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->order_id = htmlspecialchars(strip_tags($this->order_id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(
            ':payment_id',
            $this->payment_id
        );
        $stmt->bindParam(':transaction_id', $this->transaction_id);
        $stmt->bindParam(':mode', $this->mode);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    public function checkDuplicateTransactionId()
    {
        // Create query
        $query = 'SELECT * FROM ' . $this->table . ' WHERE transaction_id = :transaction_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":transaction_id", $this->transaction_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        if ($row['payment_id']) {
            return true;
        } else {
            return false;
        }
    }

    // Delete PaymentDetail
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE payment_id = :payment_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->payment_id = htmlspecialchars(strip_tags($this->payment_id));

        // Bind data
        $stmt->bindParam(':payment_id', $this->payment_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
