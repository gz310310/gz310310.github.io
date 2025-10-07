<?php
// 设置响应头为JSON格式
header("Content-Type: application/json");

// 确保请求方法是POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => '只允许POST请求']);
    exit;
}

// 获取POST数据
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// 验证数据
if (!$data || empty($data['fullName'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '姓名不能为空']);
    exit;
}

// 准备要保存的数据
$nameData = [
    'id' => $data['id'],
    'fullName' => $data['fullName'],
    'phone' => isset($data['phone']) ? $data['phone'] : '',
    'timestamp' => $data['timestamp'],
    'ip_address' => $_SERVER['REMOTE_ADDR'], // 记录提交者IP
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] // 记录用户代理
];

// 数据文件路径
$filename = 'name_data.json';

// 读取现有数据
$existingData = [];
if (file_exists($filename)) {
    $fileContent = file_get_contents($filename);
    $existingData = json_decode($fileContent, true) ?: [];
}

// 确保是数组
if (!is_array($existingData)) {
    $existingData = [];
}

// 添加新数据
$existingData[] = $nameData;

// 保存回文件
if (file_put_contents($filename, json_encode($existingData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
    echo json_encode([
        'success' => true,
        'message' => '数据保存成功',
        'data' => $nameData
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '无法保存数据到文件']);
}
?>
