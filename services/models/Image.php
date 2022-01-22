<?php

include_once __DIR__ . '/Common/ModelUtils.php';

class Image
{
    use QueryUtils;
    // DB stuff
    private $conn;
    private $table = 'image';
    // InterestedUser Properties
    public $image_id;
    public $entity_type;
    public $entity_id;
    public $path;
    public $type;
    public $created_at;
    public $updated_at;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get Image
    public function read($data)
    {
        // Create query
        $query = 'SELECT * FROM ' . $this->table;

        $query = $this->processFiltersAndOrderBy($query, $data);
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Get Single Image
    public function read_single_by_path()
    {
        // Create query
        $query =
            'SELECT * FROM ' . $this->table . ' WHERE path = :path';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":path", $this->path);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->image_id = $row['image_id'];
        $this->path = $row['path'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    // Get Single Image
    public function read_single()
    {
        // Create query
        $query =
            'SELECT * FROM ' . $this->table . ' WHERE image_id = :image_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(":image_id", $this->image_id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->image_id = $row['image_id'];
        $this->path = $row['path'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    // Get Single Image
    public function readByEntity()
    {
        // Create query
        $query =
            'SELECT * FROM ' . $this->table . ' WHERE entity_id = :entity_id AND entity_type = :entity_type';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':entity_id', $this->entity_id);
        $stmt->bindParam(':entity_type', $this->entity_type);

        // Execute query
        $stmt->execute();
        $num = $stmt->rowCount();

        $image_arr = array();
        if ($num > 0) {
            // Events array

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $image = array(
                    "path" =>  $path,
                    "image_id" => $image_id
                );
                array_push($image_arr, $image);
            }
        }


        return $image_arr;
    }

    // Create IntestedUser
    public function create()
    {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' SET '
            . ' path = :path, type = :type, created_at = :created_at,'
            . ' entity_type = :entity_type, entity_id = :entity_id, updated_at = :updated_at';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->entity_id = htmlspecialchars(strip_tags($this->entity_id));
        $this->path = htmlspecialchars(strip_tags($this->path));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->entity_type = htmlspecialchars(strip_tags($this->entity_type));
        $this->created_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':entity_id', $this->entity_id);
        $stmt->bindParam(':path', $this->path);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':entity_type', $this->entity_type);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Update InterestedUser
    public function update()
    {
        // Create query
        $query = 'UPDATE ' . $this->table . ' SET image_id = :image_id,'
            . ' path = :path, type = :type, entity_id = :entity_id, entity_type = :entity_type, '
            . ' updated_at = :updated_at WHERE image_id = :image_id';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->image_id = htmlspecialchars(strip_tags($this->image_id));
        $this->path = htmlspecialchars(strip_tags($this->path));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->updated_at = htmlspecialchars(strip_tags(date("Y-m-d H:i:s")));

        // Bind data
        $stmt->bindParam(':image_id', $this->image_id);
        $stmt->bindParam(':path', $this->path);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':entity_type', $this->entity_type);
        $stmt->bindParam(':entity_id', $this->entity_id);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete Image
    public function delete()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE image_id = :image_id';

        // $filename  = __DIR__ . "/../" . substr($this->path, strpos($this->path, "uploads"));

        // if (file_exists($filename)) {
        //     unlink($filename);
        // } else {
        //     message_logger("Image file does not exist with id: " . $this->image_id);
        // }

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->image_id = htmlspecialchars(strip_tags($this->image_id));

        // Bind data
        $stmt->bindParam(':image_id', $this->image_id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete Image
    public function deleteByEntity()
    {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE entity_id = :entity_id AND entity_type = :entity_type';

        $associated_images = $this->readByEntity();
        message_logger("before foreach: " . $this->entity_id . ', ' . $this->entity_type);
        foreach ($associated_images as $associated_image) {
            echo ' paths: ' . $associated_image["path"];
            $filename  = __DIR__ . "/../" . substr($associated_image["path"], strpos($associated_image["path"], "uploads"));
            message_logger("Image file to be deleted: " . $filename);
            if (file_exists($filename)) {
                unlink($filename);
                message_logger("Image file deleted: " . $filename);
            } else {
                message_logger("Image file does not exist with id: " . $associated_image["image_id"]);
            }
        }

        $stmt = $this->conn->prepare($query);
        // Bind data
        $stmt->bindParam(':entity_id', $this->entity_id);
        $stmt->bindParam(':entity_type', $this->entity_type);

        echo $query . ', id: ' . $this->entity_id . ', type: ' . $this->entity_type;
        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        // Prepare statement

    }
}
