<?php
class Member {
    private $idNumber;
    private $firstName;
    private $lastName;
    private $email;
    private $band;
    /**
     * @var mysqli
     */
    private $conn;

    public function __construct($data = null) {
        $this->idNumber = isset($data['id_number']) ? $data['id_number'] : '';
        $this->firstName = isset($data['first_name']) ? $data['first_name'] : '';
        $this->lastName = isset($data['last_name']) ? $data['last_name'] : '';
        $this->email = isset($data['email']) ? $data['email'] : '';
        $this->band = isset($data['band']) ? $data['band'] : '';
        global $conn;
        $this->conn = $conn;
    }

    //get all members
    public function getAll() {
        $members = array();
        $sql = "SELECT * FROM members order by id";
        try {
            $result = $this->conn->query($sql);
            $rows = $result->fetch_all(MYSQLI_ASSOC);

            foreach ($rows as $row) {
                $memberData = [
                    'id' => $row['id'],
                    'id_number' => $row['id_number'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'band' => $row['band']
                ];
                $members[] = $memberData;
            }
            return $members;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    //get specific member by its ID
    public function getMemberByID($id) {
        $sql = "SELECT * FROM members where id=$id";
        try {
            $result = $this->conn->query($sql);
            $member = $result->fetch_assoc();
            return $member;
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }

    //Create or update member
    public function save($id = null) {
//if id sent by the controller - define an update query, if no id - insert query
        if($id) {
            $sql = "UPDATE members SET id_number = ?,first_name = ?, last_name = ?, email = ?, band = ?  
WHERE id=$id";
        }
        else {
            $sql = "INSERT INTO members (id_number,first_name, last_name, email, band) VALUES (?, ?, ?, ?, ?)";
        }
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssss", $this->idNumber, $this->firstName, $this->lastName, $this->email, $this->band);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    //delete member
    public function delete($id) {
        $sql = "DELETE from members where id=$id";
        try {
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    //crate the members table
    public function createTable() {
            $sql = "CREATE TABLE `members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_number` varchar(45) NOT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `band` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_numebr_UNIQUE` (`id_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            try {
                $stmt = $this->conn->prepare($sql);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            } catch (Exception $e) {
                return $e->getMessage();
            }
    }
}
