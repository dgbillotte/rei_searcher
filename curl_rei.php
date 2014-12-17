<?php

$orig_url = 'http://www.rei.com/rest/search/results?ir=q%3A{ST}&q={ST}&page=1&pagesize=30&version=v2';

$http_headers = ['Pragma: no-cache',
    'Connection: keep-alive',
    'Cache-Control: no-cache'];

$filename = '/tmp/rei_search_results.json';

$img_base_url = 'http://www.rei.com/zoom/';
$zoom = 250;
$results = false;
$q = $safe_q = '';
if(isset($_GET['q'])) {
    $q = trim($_GET['q']);
    $safe_q = preg_replace('/\s+/', '+', $q);
    $url = preg_replace('/{ST}/', $safe_q, $orig_url);
    $ch = curl_init($url);
    $fp = fopen($filename, "w");

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);

    curl_exec($ch);
    curl_close($ch);
    fclose($fp);

    $handle = fopen($filename, 'r');
    $json_response = fread($handle, filesize($filename));

    $response = json_decode($json_response);

    $results = $response->results;
    print_r($results[0]);
}
?>
<html>
    <body>
        <div>
          <form action="" method="GET">
                <input type="text" name="q" value="<?php echo $q; ?>">
                <button>Search REI</button>
            </form>
        </div>
        <?php foreach($results as $r) { ?>
        <div>
            <div><?php echo $r->title; ?></div>
            <div><?php echo $r->benefit; ?></div>
            <div><?php echo $r->regularPrice; ?></div>
            <a href="http://www.rei.com<?php echo $r->link; ?>">
                <img src="<?php echo $img_base_url . $r->imageId . '/' . $zoom; ?>">
            </a>
        </div>
        <?php } ?>
    </body>
</html>