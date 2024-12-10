<?php
include("../includes/gzip.inc.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("../includes/server.inc.php");
class GroupGenerator {
    private $conn;
    private $users = [];
    private $groups = [];
    private $userGroups = [];  // Track number of groups per user
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->loadUsers();
    }
    
    private function loadUsers() {
        $query = "SELECT id, first_name, last_name FROM customerdata WHERE admin = 0";
        $result = $this->conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $this->users[] = $row;
            $this->userGroups[$row['id']] = 0;
        }
    }
    
    private function isValidGroup($potentialGroup) {
        // Check if any three members are already in another group together
        $groupSize = count($potentialGroup);
        for ($i = 0; $i < $groupSize - 2; $i++) {
            for ($j = $i + 1; $j < $groupSize - 1; $j++) {
                for ($k = $j + 1; $k < $groupSize; $k++) {
                    $members = [$potentialGroup[$i], $potentialGroup[$j], $potentialGroup[$k]];
                    foreach ($this->groups as $existingGroup) {
                        $commonMembers = array_intersect($members, $existingGroup);
                        if (count($commonMembers) >= 3) {
                            return false;
                        }
                    }
                }
            }
        }
        
        // Check if any user would exceed their group limit
        foreach ($potentialGroup as $userId) {
            if ($this->userGroups[$userId] >= 3) {
                return false;
            }
        }
        
        return true;
    }
    
    private function updateUserGroupCounts($group) {
        foreach ($group as $userId) {
            $this->userGroups[$userId]++;
        }
    }
    
    public function generateGroups() {
        $totalUsers = count($this->users);
        $attempts = 0;
        $maxAttempts = 1000;  // Prevent infinite loops
        
        while ($attempts < $maxAttempts) {
            $availableUsers = array_filter($this->users, function($user) {
                return $this->userGroups[$user['id']] < 3;
            });
            
            if (count($availableUsers) < 5) {
                break;  // Not enough users left to form a group
            }
            
            // Randomly select 5 users
            $shuffledUsers = $availableUsers;
            shuffle($shuffledUsers);
            $potentialGroup = array_slice(array_column($shuffledUsers, 'id'), 0, 5);
            
            if ($this->isValidGroup($potentialGroup)) {
                $this->groups[] = $potentialGroup;
                $this->updateUserGroupCounts($potentialGroup);
            }
            
            $attempts++;
        }
    }
    
    public function printGroups() {
        echo "<h2>Generated Groups</h2>";
        foreach ($this->groups as $index => $group) {
            echo "<h3>Group " . ($index + 1) . "</h3>";
            echo "<ul>";
            foreach ($group as $userId) {
                $user = array_filter($this->users, function($u) use ($userId) {
                    return $u['id'] == $userId;
                });
                $user = reset($user);
                echo "<li>" . htmlspecialchars($user['first_name'] . " " . $user['last_name']) . "</li>";
            }
            echo "</ul>";
        }
        
        echo "<h3>Summary</h3>";
        echo "<p>Total groups generated: " . count($this->groups) . "</p>";
        echo "<p>Users and their group counts:</p>";
        echo "<ul>";
        foreach ($this->users as $user) {
            echo "<li>" . htmlspecialchars($user['first_name'] . " " . $user['last_name']) . 
                 ": " . $this->userGroups[$user['id']] . " groups</li>";
        }
        echo "</ul>";
    }
    
    public function saveGroups() {
        // First, create a groups table if it doesn't exist
        $createTableSQL = "CREATE TABLE IF NOT EXISTS user_groups (
            group_id INT AUTO_INCREMENT PRIMARY KEY,    
            group_number INT NOT NULL,
            user_id INT NOT NULL,
            FOREIGN KEY (user_id) REFERENCES customerdata(id)
        )";
        $this->conn->query($createTableSQL);
        
        // Clear existing groups
        $this->conn->query("TRUNCATE TABLE user_groups");
        
        // Save new groups
        $stmt = $this->conn->prepare("INSERT INTO user_groups (group_number, user_id) VALUES (?, ?)");
        foreach ($this->groups as $groupNumber => $group) {
            foreach ($group as $userId) {
                $groupNum = $groupNumber + 1;
                $stmt->bind_param("ii", $groupNum, $userId);
                $stmt->execute();
            }
        }
    }

}

//Usage
// $generator = new GroupGenerator($conn);
// $generator->generateGroups();
// //$generator->printGroups();
// $generator->saveGroups();
?>