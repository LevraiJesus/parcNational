<?php
use Firebase\JWT\JWT;

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $password;
    public $email;
    public $name;
    public $firstname;
    public $phoneNumber;
    public $browsingHistory;
    public $gpsHistory;
    public $admin;
    public $createdAt;
    public $modifiedAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET email=:email, password=:password, name=:name, firstname=:firstname, phoneNumber=:phoneNumber, admin=:admin, created_at=NOW(), modified_at=NOW()";
    
        $stmt = $this->conn->prepare($query);
    
        $this->sanitize();
    
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":phoneNumber", $this->phoneNumber);
        $stmt->bindParam(":admin", $this->admin);
        
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }
    

    public function read($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->name = $row['name'];
            $this->firstname = $row['firstname'];
            $this->phoneNumber = $row['phoneNumber'];
            $this->browsingHistory = $row['browsingHistory'];
            $this->gpsHistory = $row['gpsHistory'];
            $this->admin = $row['admin'];
            $this->createdAt = $row['created_at'];
            $this->modifiedAt = $row['modified_at'];
            return true;
        }
        
        return false;
    }
    

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET email = :email, name = :name, firstname = :firstname, 
                      phoneNumber = :phoneNumber, admin = :admin, 
                      modified_at = NOW() 
                  WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
    
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->phoneNumber = htmlspecialchars(strip_tags($this->phoneNumber));
    
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":phoneNumber", $this->phoneNumber);
        $stmt->bindParam(":admin", $this->admin);
        $stmt->bindParam(":id", $this->id);
    
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }
    
    public function updatePassword($newPassword, $id = null) {
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
    
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $userId = $id ?? $this->id;
    
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":id", $userId);
    
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }    
    

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
    
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }
    
    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function generateJWT() {
        $secretKey = $_ENV['JWT_SECRET'];
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // Token valid for 1 hour
    
        $payload = [
            'user_id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'firstname' => $this->firstname,
            'role' => $this->admin ? 'admin' : 'user',
            'iat' => $issuedAt,
            'exp' => $expirationTime
        ];
    
        return JWT::encode($payload, $secretKey, 'HS256');
    }
    
    
    public function getAllUsers() {
        $query = "SELECT id, email, name, firstname, phoneNumber, browsingHistory, gpsHistory, admin FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function sanitize(){
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->phoneNumber = htmlspecialchars(strip_tags($this->phoneNumber));
    }
}
