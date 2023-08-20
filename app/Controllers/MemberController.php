<?php
include 'app/Models/Member.php';
class MemberController {

    private $conn;
    public function __construct($conn) {
        global $conn;
        $this->conn = $conn;
    }

    //get all members and return to the client the members json or error
    public function getAllMembers()
    {
        $member = new Member();
        try {
            $membersData = $member->getAll();
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode($membersData);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    //get specific member id and return to the client the member json or error
    public function getMember($id){
        $member = new Member();
        try {
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode($member->getMemberByID($id));

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    //add or edit member,
    public function addOrEditMember($id = null) {
        try {
            /*if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $_POST;*/
            //check if request method from the client is get/post
            if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' ) {
                //get the BODY from the request and convert it to json
                $inputData = file_get_contents('php://input');
                $data = json_decode($inputData, true);
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Invalid request method.']);
                return;
            }
            //create array with the request data json to fill the member data
            $memberData = [
                'id_number' => $data['id_number'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'band' => $data['band']
            ];
            //call the method to save the data in the database and return the response
            $member = new Member($memberData);
            $result = $member->save($id);
            if ($result === true) {
                echo json_encode(['success' => true, 'message' => 'Member saved successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => false, 'message' => 'Failed to save member,' . $result]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    //delete member
    public function deleteMember($id){
        //call the method to delete the member form database and return the response
        $member = new Member();
        $result = $member->delete($id);
        header('Content-Type: application/json');
        if ($result === true) {
            echo json_encode(['success' => true, 'message' => 'Member deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => false, 'message' => 'Failed to delete member,'.$result]);
        }
    }

    //call the view for showing the member form
    public function showMemberForm($id = null) {
        include 'public/views/member_form.php';
    }

    //create member table in database
    public function createMemberTable($credentials){
        //get the creditability from the client url seperate with :
        // - username:password and check if the credentials match the app admin
        // credentials in env file.
        list($username, $password) = explode(':', $credentials);
        if($username === $_ENV['ADMIN_USERNAME'] && $password === $_ENV['ADMIN_PASS']) {
            //call the method to create the table in the database and return the response
            $member = new Member();
            $result = $member->createTable();
            header('Content-Type: application/json');
            if ($result === true) {
                echo json_encode(['success' => true, 'message' => 'Table members has been created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => false, 'message' => 'Failed to create members table,'.$result]);
            }
        }
        else {
            http_response_code(401);
            echo json_encode(['error' => 'Credentials are not valid.']);
        }
    }

}
