<?php
// Replace with your GitHub Personal Access Token
$token = '';

// Function to fetch data from GitHub API
function fetch_github_data($url, $token) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "User-Agent: request",
        "Authorization: token {$token}"
    ]);

    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        return null; // Handle error appropriately
    }

    return json_decode($result, true);
}

// Fetch top 200 GitHub users in the Philippines by followers
function get_top_users_by_followers($token) {
    $url1 = "https://api.github.com/search/users?q=location:Philippines&sort=followers&order=desc&per_page=100&page=1";
    $page1 = fetch_github_data($url1, $token)['items'] ?? [];

    $url2 = "https://api.github.com/search/users?q=location:Philippines&sort=followers&order=desc&per_page=100&page=2";
    $page2 = fetch_github_data($url2, $token)['items'] ?? [];

    return array_merge($page1, $page2);
}

// Fetch top 200 GitHub users in the Philippines by public repositories
function get_top_users_by_repos($token) {
    $url1 = "https://api.github.com/search/users?q=location:Philippines&sort=repositories&order=desc&per_page=100&page=1";
    $page1 = fetch_github_data($url1, $token)['items'] ?? [];

    $url2 = "https://api.github.com/search/users?q=location:Philippines&sort=repositories&order=desc&per_page=100&page=2";
    $page2 = fetch_github_data($url2, $token)['items'] ?? [];

    return array_merge($page1, $page2);
}

// Fetch detailed user information (followers, repos, etc.)
function get_user_details($users, $token) {
    $user_details = [];
    foreach ($users as $user) {
        $username = $user['login'];
        $user_data = fetch_github_data("https://api.github.com/users/{$username}", $token);
        if ($user_data === null) continue;

        $user_details[] = [
            'username' => $username,
            'profile_url' => "https://github.com/{$username}",
            'profile_image_url' => $user['avatar_url'],
            'followers' => $user_data['followers'] ?? 0,
            'repos' => $user_data['public_repos'] ?? 0,
            'location' => $user_data['location'] ?? 'City not available'
        ];
    }
    return $user_details;
}

// Generate markdown links for specific sorted categories
function generate_category_links() {
    return "## Category Links\n- [Top GitHub Users by Followers](#top-github-users-by-followers)\n- [Top GitHub Users by Public Repositories](#top-github-users-by-repositories)\n";
}

// Generate markdown for a specific sorted category
function generate_category_markdown($users, $category) {
    $markdown = "### Top GitHub Users by {$category}\n";
    $markdown .= "| Rank | Username | Followers | Repositories | City | Profile |\n| --- | --- | --- | --- | --- | --- |\n";

    $rank = 1;
    foreach ($users as $user) {
        $username = $user['username'];
        $profile_url = $user['profile_url'];
        $profile_image_url = $user['profile_image_url'];
        $followers_count = $user['followers'];
        $repos_count = $user['repos'];
        $location = $user['location'];

        // Use HTML to resize the image
        $image_html = "<img src='{$profile_image_url}' alt='{$username}' width='40' height='40'>";
        
        // Append to markdown
        $markdown .= "| {$rank} | {$image_html} [{$username}]({$profile_url}) | {$followers_count} | {$repos_count} | {$location} | [Profile]({$profile_url}) |\n";
        $rank++;
    }

    return $markdown;
}

// Generate the full markdown output
function generate_markdown($followers_users, $repo_users, $filename) {
    // Get current datetime
    $current_datetime = date('Y/m/d h:i A T');
    $city_count = count(array_unique(array_map(function($user) {
        return $user['location'];
    }, $followers_users)));

    // Start generating markdown
    $markdown = "# GitHub Users in the Philippines\n";
    $markdown .= "![GitHub Philippines](makabayan.jpg)\n\n";

    // Overview
    $markdown .= "### Overview\n";
    $markdown .= "This is a dynamically updated list of GitHub users from the Philippines, showcasing users from cities across the country as of {$current_datetime}.\n";
    $markdown .= "This list contains users from **{$city_count} cities** across the Philippines.\n\n";
    $markdown .= "\n**This is a list of the top 200 GitHub users in the Philippines, based on the location set in their GitHub profiles.**\n";

    // Generate category links
    $markdown .= generate_category_links();
    
    // Generate markdown for followers and repos
    $markdown .= generate_category_markdown($followers_users, 'Followers');
    $markdown .= generate_category_markdown($repo_users, 'Public Repositories');

    // Add a final note
    $markdown .= "\nThank you for checking out the list of GitHub users in the Philippines! Don't forget to contribute to the open-source community, and feel free to ⭐ star this repository!";

    // Write markdown to file
    file_put_contents($filename, $markdown);
}

// Main script
// Fetch top 200 users by followers and top 200 users by repositories separately
$follower_users = get_top_users_by_followers($token);
$repo_users = get_top_users_by_repos($token);

// Fetch detailed information for each list
$followers_details = get_user_details($follower_users, $token);
$repo_details = get_user_details($repo_users, $token);

// Generate the markdown file
generate_markdown($followers_details, $repo_details, 'README.md');

echo "Markdown file generated: README.md";
?>
    