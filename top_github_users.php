<?php
// Fetch data from GitHub API
function fetch_github_data($url) {
    $options = [
        'http' => [
            'header'  => "User-Agent: request\r\n",
            'method'  => 'GET',
        ]
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
}

// Fetch top followed users in the Philippines
function get_top_followed_users() {
    $url = "https://api.github.com/search/users?q=location:Philippines&sort=followers&order=desc&per_page=200";
    return fetch_github_data($url)['items'];
}

// Generate markdown output
function generate_markdown($users, $filename) {
    $markdown = "# Top Followed GitHub Users in the Philippines\n\n";
    $markdown .= "| Rank | Username | Followers | Repositories | Profile |\n";
    $markdown .= "| --- | --- | --- | --- | --- |\n";

    $rank = 1;
    foreach ($users as $user) {
        $username = $user['login'];
        $followers_url = $user['followers_url'];
        $repos_url = $user['repos_url'];
        $profile_url = "https://github.com/" . $username;

        // Get follower count
        $user_data = fetch_github_data("https://api.github.com/users/{$username}");
        $followers_count = $user_data['followers'];
        $repos_count = $user_data['public_repos'];

        $markdown .= "| {$rank} | {$username} | {$followers_count} | {$repos_count} | [Profile]({$profile_url}) |\n";
        $rank++;
    }

    // Write markdown to file
    file_put_contents($filename, $markdown);
}

// Main script
$users = get_top_followed_users();
generate_markdown($users, 'README.md');

echo "Markdown file generated: README.md";
?>
