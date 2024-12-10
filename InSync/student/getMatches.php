<?php
// Include the server connection file to ensure database connection
include("../includes/gzip.inc.php");
require_once("../includes/server.inc.php");

function getMatches() {
    global $conn; // Use the global database connection

    if (!$conn) {
        error_log("Database connection failed in getMatches()");
        return array();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        error_log("User not logged in when trying to get matches");
        return array();
    }

    $current_user_id = $_SESSION['user_id'];

    try {
        // First, get the current user's tags
        $user_query = "SELECT tags FROM customerdata WHERE id = ?";
        $stmt = $conn->prepare($user_query);
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        $user_data = $user_result->fetch_assoc();

        // If no user data found, return empty matches
        if (!$user_data || empty($user_data['tags'])) {
            return array();
        }

        $user_tags = json_decode($user_data['tags'], true) ?: array();

        $matches_query = "SELECT 
            id,
            first_name,
            last_name,
            tags
        FROM customerdata 
        WHERE id != ? AND admin = 0";

        $stmt = $conn->prepare($matches_query);
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $matches = array();
        while ($row = $result->fetch_assoc()) {
            $potential_match_tags = json_decode($row['tags'], true) ?: array();
            
            if (!empty($user_tags) && !empty($potential_match_tags)) {
                $common_tags = array_values(array_intersect($user_tags, $potential_match_tags));
                $match_count = count($common_tags);
                
                // Calculate match percentage
                $min_tag_count = min(count($user_tags), count($potential_match_tags));
                if ($min_tag_count > 0) {
                    $match_percentage = ($match_count / $min_tag_count) * 100;
                    
                    // Only include if 50% or more tags match
                    if ($match_percentage >= 50) {
                        $matches[] = array(
                            'id' => $row['id'],
                            'first_name' => $row['first_name'],
                            'last_name' => $row['last_name'],
                            'common_tags' => $common_tags,
                            'match_percentage' => $match_percentage,
                            'match_count' => $match_count
                        );
                    }
                }
            }
        }

        // Sort matches by percentage
        usort($matches, function($a, $b) {
            return $b['match_percentage'] <=> $a['match_percentage'];
        });

        // Limit to top 5 matches
        return array_slice($matches, 0, 5);

    } catch (Exception $e) {
        error_log("Error in getMatches: " . $e->getMessage());
        return array();
    }
}
?>