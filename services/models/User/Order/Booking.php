<?php

include_once __DIR__ . "/../../Common/ModelUtils.php";

class Booking
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table;

    // Booking Properties
    public $booking_id;
    public $product_id;
    public $quantity;
    public $price;
    public $order_id;
    public $product_name;
    public $product_type;
    public $product_image;
    public $status;
    public $ticket_no;
    public $venue;
    public $time;
    public $user_id;
    public $created_at;
    public $updated_at;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = TABLES::$BOOKING;
    }

    // Get Booking
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
	
	  // Get Booking
    public function readBookingsAndPayment($data)
    {
        // Create query
        $query = 'SELECT bk.*, au.app_user_id, au.first_name, au.last_name, au.email_id, au.mobile_no, pd.transaction_id FROM ' . $this->table . ' bk LEFT JOIN '
            . 'app_user au on bk.user_id = au.app_user_id LEFT JOIN payment_detail pd on bk.order_id = pd.order_id order by bk.booking_id desc';

        $query = $this->processFiltersAndOrderBy($query, $data);

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $this->executeQuery($stmt);

        return $stmt;
    }


    // Get Single Booking
    public function read_single()
    {
        // Create query
        $query = 'SELECT * FROM ' . $this->table . ' WHERE booking_id = :booking_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":booking_id", $this->booking_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->booking_id = $row['booking_id'];
        $this->product_id = $row['product_id'];
        $this->quantity = $row['quantity'];
        $this->price = $row['price'];
        $this->order_id = $row['order_id'];
        $this->product_name = $row['product_name'];
        $this->product_type = $row['product_type'];
        $this->product_image = $row['product_image'];
        $this->status = $row['status'];
        $this->ticket_no = $row['ticket_no'];
        $this->venue = $row['venue'];
        $this->time = $row['time'];
        $this->user_id = $row['user_id'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    // Create Booking
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET price = :price, product_id = :product_id,'
            . ' quantity = :quantity, order_id = :order_id, product_name = :product_name, product_type = :product_type,'
            . ' product_image = :product_image, status = :status, ticket_no = :ticket_no, '
            . ' venue = :venue, time = :time, user_id = :user_id, created_at = :created_at, updated_at = :updated_at';


        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->product_name = htmlspecialchars(strip_tags($this->product_name));
        $this->product_type = htmlspecialchars(strip_tags($this->product_type));
        $this->product_image = htmlspecialchars(strip_tags($this->product_image));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->ticket_no = htmlspecialchars(strip_tags($this->ticket_no));
        $this->venue = htmlspecialchars(strip_tags($this->venue));
        $this->time = htmlspecialchars(strip_tags($this->time));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':product_type', $this->product_type);
        $stmt->bindParam(':product_image', $this->product_image);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':ticket_no', $this->ticket_no);
        $stmt->bindParam(':venue', $this->venue);
        $stmt->bindParam(':time', $this->time);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update Booking
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET price = :price, product_id = :product_id,'
            . ' quantity = :quantity, order_id = :order_id, product_name = :product_name, product_type = :product_type,'
            . ' product_image = :product_image, status = :status, ticket_no = :ticket_no, '
            . ' venue = :venue, time = :time, user_id = :user_id, updated_at = :updated_at WHERE booking_id = :booking_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->product_name = htmlspecialchars(strip_tags($this->product_name));
        $this->product_type = htmlspecialchars(strip_tags($this->product_type));
        $this->product_image = htmlspecialchars(strip_tags($this->product_image));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->ticket_no = htmlspecialchars(strip_tags($this->ticket_no));
        $this->venue = htmlspecialchars(strip_tags($this->venue));
        $this->time = htmlspecialchars(strip_tags($this->time));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':product_type', $this->product_type);
        $stmt->bindParam(':product_image', $this->product_image);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':ticket_no', $this->ticket_no);
        $stmt->bindParam(':venue', $this->venue);
        $stmt->bindParam(':time', $this->time);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':booking_id', $this->booking_id);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete Booking
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE booking_id = :booking_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->booking_id = htmlspecialchars(strip_tags($this->booking_id));

        // Bind data
        $stmt->bindParam(':booking_id', $this->booking_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
