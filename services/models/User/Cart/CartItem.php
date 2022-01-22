<?php

include_once __DIR__ . "/../../Common/ModelUtils.php";

class CartItem
{

    use QueryUtils;

    // DB stuff
    private $conn;
    private $table;

    // CartItem Properties
    public $cart_item_id;
    public $cart_id;
    public $product_id;
    public $price;
    public $quantity;
    public $product_type;
    public $product_slug;
    public $product_name;
    public $product_image;
    public $venue;
    public $event_date;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
        $this->table = TABLES::$CART_ITEM;
    }

    // Get CartItems
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

    // Get Single CartItem
    public function read_single()
    {
        // Create query
        $query = 'SELECT * FROM ' . $this->table . ' WHERE cart_item_id = :cart_item_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":cart_item_id", $this->cart_item_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->cart_item_id = $row['cart_item_id'];
        $this->cart_id = $row['cart_id'];
        $this->product_id = $row['product_id'];
        $this->product_slug = $row['slug'];
        $this->price = $row['price'];
        $this->quantity = $row['quantity'];
        $this->product_name = $row['product_name'];
        $this->product_type = $row['product_type'];
        $this->product_image = $row['product_image'];
        $this->event_date = $row['event_date'];
        $this->venue = $row['venue'];
    }

    // Create IntestedUser
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET cart_id = :cart_id, product_id = :product_id, '
            . ' price = :price, quantity = :quantity, product_name = :product_name, product_slug = :product_slug,'
            . ' product_image = :product_image, product_type = :product_type, venue = :venue, event_date =:event_date';


        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->cart_id = htmlspecialchars(strip_tags($this->cart_id));
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->product_slug = htmlspecialchars(strip_tags($this->product_slug));
        $this->product_name = htmlspecialchars(strip_tags($this->product_name));
        $this->product_type = htmlspecialchars(strip_tags($this->product_type));
        $this->product_image = htmlspecialchars(strip_tags($this->product_image));

        // Bind data
        $stmt->bindParam(':cart_id', $this->cart_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':product_slug', $this->product_slug);
        $stmt->bindParam(':product_type', $this->product_type);
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':product_image', $this->product_image);
        $stmt->bindParam(':venue', $this->venue);
        $stmt->bindParam(':event_date', $this->event_date);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return
                $this->conn->lastInsertId();
        }

        return false;
    }

    // Update CartItem
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table .  ' SET cart_id = :cart_id, product_id = :product_id, '
            . ' price = :price, quantity = :quantity, product_name = :product_name, product_slug = :product_slug,' .
            'product_type = :product_type, product_image = :product_image, venue = :venue, event_date =:event_date' .
            ' WHERE cart_item_id = :cart_item_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->cart_id = htmlspecialchars(strip_tags($this->cart_id));
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->product_slug = htmlspecialchars(strip_tags($this->product_slug));
        $this->product_name = htmlspecialchars(strip_tags($this->product_name));
        $this->product_type = htmlspecialchars(strip_tags($this->product_type));
        $this->product_image = htmlspecialchars(strip_tags($this->product_image));

        // Bind data
        $stmt->bindParam(
            ':cart_item_id',
            $this->cart_item_id
        );
        $stmt->bindParam(':cart_id', $this->cart_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':product_slug', $this->product_slug);
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':product_type', $this->product_type);
        $stmt->bindParam(':product_image', $this->product_image);
        $stmt->bindParam(':venue', $this->venue);
        $stmt->bindParam(':event_date', $this->event_date);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }

    // Delete CartItem
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE cart_item_id = :cart_item_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->cart_item_id = htmlspecialchars(strip_tags($this->cart_item_id));

        // Bind data
        $stmt->bindParam(':cart_item_id', $this->cart_item_id);

        // Execute query
        if ($this->executeQuery($stmt)) {
            return true;
        }

        return false;
    }
}
