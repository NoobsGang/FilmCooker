<?php
header('Content-Type: application/json');

// TMDb API configuration
$TMDB_API_KEY = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJmOWVmNDdkMjU0ZTFmNzg4YzgyY2QzY2Y5NDc5NzNhNCIsIm5iZiI6MTczNjE2MzI1MS4xMiwic3ViIjoiNjc3YmJmYjNkOWIwZTcwZDVkNzI0OGVmIiwic2NvcGVzIjpbImFwaV9yZWFkIl0sInZlcnNpb24iOjF9.yHGQcDFXiaghtuwLizkHL9YlqS6k0p7aapzgnrSFWxI'; // Replace with your TMDb API key
$TMDB_BASE_URL = 'https://api.themoviedb.org/3';

// Function to make TMDb API call
function callTmdbApi($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Optional: Disable SSL verification for local testing (use with caution in production)
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode != 200) {
        return json_encode(['error' => 'Failed to fetch data from TMDb, HTTP code: ' . $httpCode]);
    }

    return $response;
}

// Handle different API endpoints based on the 'action' parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'discover':
        $language = isset($_GET['language']) ? $_GET['language'] : 'en';
        $genres = isset($_GET['genres']) ? $_GET['genres'] : '';
        $rating = isset($_GET['rating']) ? $_GET['rating'] : '';

        $url = "$TMDB_BASE_URL/discover/movie?include_adult=true&include_video=false&language=en-IN&page=1&sort_by=popularity.desc&with_genres=" . urlencode($genres) . "&with_origin_country=IN&with_original_language=$language";
        if ($rating) {
            $url .= "&certification_country=US&certification=$rating";
        }

        $url .= "&api_key=$TMDB_API_KEY";
        echo callTmdbApi($url);
        break;

    case 'movie_details':
        $movieId = isset($_GET['movie_id']) ? $_GET['movie_id'] : '';
        $language = isset($_GET['language']) ? $_GET['language'] : 'en';

        if (!$movieId) {
            echo json_encode(['error' => 'Movie ID is required']);
            exit;
        }

        $url = "$TMDB_BASE_URL/movie/$movieId?language=en&append_to_response=credits&api_key=$TMDB_API_KEY";
        echo callTmdbApi($url);
        break;

    case 'watch_providers':
        $movieId = isset($_GET['movie_id']) ? $_GET['movie_id'] : '';

        if (!$movieId) {
            echo json_encode(['error' => 'Movie ID is required']);
            exit;
        }

        $url = "$TMDB_BASE_URL/movie/$movieId/watch/providers?api_key=$TMDB_API_KEY";
        echo callTmdbApi($url);
        break;

    case 'genres':
        $url = "$TMDB_BASE_URL/genre/movie/list?language=en-US&api_key=$TMDB_API_KEY";
        echo callTmdbApi($url);
        break;

    case 'languages':
        $url = "$TMDB_BASE_URL/configuration/languages?api_key=$TMDB_API_KEY";
        echo callTmdbApi($url);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
