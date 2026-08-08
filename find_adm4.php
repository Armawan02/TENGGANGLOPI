<?php
$adm2_list = [
    '76.02', // Mamuju
    '73.08', // Maros
    '73.01', // Selayar
    '73.22', // Luwu Utara
    '73.06'  // Gowa
];

$mh = curl_multi_init();
$curl_handles = [];

foreach ($adm2_list as $adm2) {
    for ($kec = 1; $kec <= 3; $kec++) {
        $kec_str = str_pad($kec, 2, '0', STR_PAD_LEFT);
        for ($desa = 1001; $desa <= 1010; $desa++) {
            $adm4 = "{$adm2}.{$kec_str}.{$desa}";
            $url = "https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4={$adm4}";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            curl_multi_add_handle($mh, $ch);
            $curl_handles[$adm4] = $ch;
        }
    }
}

$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running);

foreach ($curl_handles as $adm4 => $ch) {
    $info = curl_getinfo($ch);
    if ($info['http_code'] == 200) {
        $response = curl_multi_getcontent($ch);
        if (strpos($response, 'lokasi') !== false) {
            echo "FOUND: " . $adm4 . "\n";
            // Print only the first one for each adm2
            // We'll just print them all, there won't be many
        }
    }
    curl_multi_remove_handle($mh, $ch);
}
curl_multi_close($mh);
?>
