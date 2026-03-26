<?php
header('Content-Type: application/json');

$api_key = 'AIzaSyBUJgbHP3hL4QwGxw7PO8RmxvG7hYlim8w';
$model = "gemini-1.5-flash";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$action = $_POST['action'] ?? 'summary';
$files = $_FILES['docs'];

$prompts = [
    "summary" => "Summarize these documents comprehensively in bullet points.",
    "csv"     => "Extract all data and key metrics into a CSV format table.",
    "report"  => "Write a structured professional report based on these documents.",
    "excel"    => "Extract names, dates, and key entities into a excel table."
];
$userPrompt = $prompts[$action];
$parts = [
    ["text" => $userPrompt]
];

foreach ($files['tmp_name'] as $index => $tmpName) {
    if ($files['error'][$index] === UPLOAD_ERR_OK) {
        $mimeType = $files['type'][$index];
        $fileData = base64_encode(file_get_contents($tmpName));
        
        $parts[] = [
            "inline_data" => [
                "mime_type" => $mimeType,
                "data" => $fileData
            ]
        ];
    }
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$api_key}";

$payload = [
    "contents" => [
        [
            "parts" => $parts
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$resData = json_decode($response, true);

// 5. Return the result to Javascript
if ($httpCode === 200 && isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
    $aiText = $resData['candidates'][0]['content']['parts'][0]['text'];
    echo json_encode(['success' => true, 'result' => $aiText]);
} else {
    $errorMsg = $resData['error']['message'] ?? 'Unknown API Error';
    echo json_encode(['success' => false, 'error' => $errorMsg]);
}
?>