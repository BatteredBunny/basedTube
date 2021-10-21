<?php
if (!isset($_GET['id'])) {
    http_response_code('400');
    exit();
}

require('/var/www/php/header.php');

$result = pg_query_params($client, 'SELECT "name", "author", "visibility" FROM stuff.videos WHERE id=$1', array($_GET['id']));

if ($result) {
    $entry = pg_fetch_array($result);

    if ($entry['visibility'] != 2) {
        $author = htmlspecialchars(getUserById($client, $entry['author']));

        $data = array(
            'title' => htmlspecialchars($entry['name']),
            'author_name' => $author,
            'author_url' => $MAIN_DOMAIN . '/user?name=' . $author
        );
        
        header('Content-Type: application/json+oembed; charset=utf-8');
        echo json_encode($data);
    }
}
?>