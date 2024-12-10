<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Top Matches - InSync</title>
    <link rel="stylesheet" href="resources/matches.css" />
</head>

<body>
    <div class="sections">
        <section>
            <?php
            include("../includes/gzip.inc.php");
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            include("includes/nav.inc.php");
            include("includes/redirect.php");
            include("../includes/server.inc.php");

            // Get current user's ID from session
            $current_user_id = $_SESSION['user_id'] ?? null;

            if ($current_user_id) {
                // First, get the current user's tags
                $user_query = "SELECT tags FROM customerdata WHERE id = ?";
                $stmt = $conn->prepare($user_query);
                $stmt->bind_param("i", $current_user_id);
                $stmt->execute();
                $user_result = $stmt->get_result();
                $user_data = $user_result->fetch_assoc();
                $user_tags = json_decode($user_data['tags'], true);

                // Now get all other non-admin users
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

                // We'll process the matches in PHP
                $matches = array();
                while ($row = $result->fetch_assoc()) {
                    $potential_match_tags = json_decode($row['tags'], true);
                    
                    if (!empty($user_tags) && !empty($potential_match_tags)) {
                        // Find common tags
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
                $matches = array_slice($matches, 0, 5);
            }
            ?>
        </section>

        <main>
            <header>
                <h1>Your Top Matches</h1>
            </header>
            <div class="matches-list">
                <?php
                if (!empty($matches)) {
                    foreach ($matches as $index => $match) {
                        echo "<div class='matchSectionWrapper'>
                        <div class='matchSection' data-user-id='" . htmlspecialchars($match['id']) . "'>
                            <div class='match-item'>
                                <h3>#" . ($index + 1) . ": " . htmlspecialchars($match['first_name']) . " " . htmlspecialchars($match['last_name']) . "</h3>
                                <div class='match-tags'>";
                    foreach ($match['common_tags'] as $tag) {
                        echo "<span class='match-tag'>" . htmlspecialchars($tag) . "</span>";
                    }
                    echo "</div>
                                <p class='match-count'>" . $match['match_count'] . " shared interests</p>
                                <div class='match-percentage'>" . number_format($match['match_percentage'], 0) . "%</div>
                            </div>
                        </div>
                    </div>";

                    }
                } else {
                    echo "<li class='no-matches'>No matches found at this time. Try adding more tags to your profile!</li>";
                }
                //$conn->close();
                ?>
            </div>
        </main>
    </div>
</body>

</html>