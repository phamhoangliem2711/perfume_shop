<?php
// Tắt báo cáo lỗi (Tùy chọn, để ẩn các lỗi nhỏ như Deprecated/Warning)
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE); 

// =======================================================
// PHẦN 1: LẤY NỘI DUNG HTML BẰNG cURL
// =======================================================

// $url = 'https://store.steampowered.com/'; 
$url = "https://www.thegioididong.com/";
$ch = curl_init($url);

// Thiết lập cURL để lấy nội dung ổn định
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Tắt kiểm tra SSL (Chỉ dùng cho môi trường dev)

$content = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

if ($content === FALSE || $curl_error) {
    die("<h3>⚠️ Lỗi cURL: Không thể tải nội dung từ Steam. Lỗi: $curl_error</h3>");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Phân tích Dữ liệu Web Steam</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h3 { color: #1b2838; border-bottom: 2px solid #1b2838; padding-bottom: 5px; }
        pre, ul { background-color: #f4f4f4; padding: 10px; border: 1px solid #ddd; white-space: pre-wrap; word-wrap: break-word; }
        li { margin-bottom: 5px; }
        .valid { color: green; font-weight: bold; }
        .invalid { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Phân tích Dữ liệu Web Steam Store</h1>
    
    <?php
    // Pattern tìm các URL nằm trong thuộc tính href="" của thẻ <a>
    $pattern_links = '/<a\s[^>]*href=["\'](https?:\/\/[^"\']+|[^"\']+)["\'][^>]*>/i';

    preg_match_all($pattern_links, $content, $matches_links);
    ?>

    <h3>🔗 a. Các Link (URL) đã được lọc:</h3>
    <?php
    if (!empty($matches_links[1])) {
        $links = array_unique($matches_links[1]);
        $stt = 1;
        echo "<ul>";
        foreach (array_slice($links, 0, 50) as $link) { // Chỉ hiển thị 50 link đầu tiên
            // Hoàn thiện link tương đối (nếu cần)
            if (strpos($link, 'http') === false && strpos($link, '//') === false && $link != '#') {
                $link = 'https://www.thegioididong.com/' . ltrim($link, '/');
            }
            echo "<li>$stt. $link</li>";
            $stt++;
        }
        echo "</ul>";
    } else {
        echo "<p>Không tìm thấy link nào.</p>";
    }
    ?>

    <?php
    // Email:
    $pattern_email = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/i';
    preg_match_all($pattern_email, $content, $matches_email);
    
    // Số điện thoại (Pattern cơ bản cho quốc tế):
    $pattern_phone = '/(\+?\d[\d\s\.\-]{7,}\d)/'; 
    preg_match_all($pattern_phone, $content, $matches_phone);
    ?>
    
    <h3>📧 b. Các địa chỉ Email và Số điện thoại đã được lọc:</h3>
    
    <h4>Email:</h4>
    <?php
    if (!empty($matches_email[0])) {
        echo "<pre>";
        print_r(array_unique($matches_email[0]));
        echo "</pre>";
    } else {
        echo "<p>Không tìm thấy địa chỉ email nào trong nội dung chính.</p>";
    }
    ?>

    <h4>Số điện thoại:</h4>
    <?php
    if (!empty($matches_phone[0])) {
        echo "<pre>";
        print_r(array_unique($matches_phone[0]));
        echo "</pre>";
    } else {
        echo "<p>Không tìm thấy số điện thoại nào trong nội dung chính.</p>";
    }
    ?>

    <?php
    function checkImageName($filename) {
        // Quy tắc: Chỉ cho phép chữ thường (a-z), số (0-9), dấu gạch ngang (-), dấu gạch dưới (_), và dấu chấm (.)
        $pattern_valid = '/^[a-z0-9_\-]+\.(jpe?g|png|gif|webp)$/';

        if (preg_match($pattern_valid, $filename)) {
            return "<span class='valid'>Hợp lệ</span>";
        } else {
            return "<span class='invalid'>Không hợp lệ</span>";
        }
    }

    $test_images = [
        'game-csgo-logo.jpg',
        'Game Logo.png',
        'logo_steam_01.webp',
        'hinh-anh-moi#1.gif',
        'steam-community-icon.png'
    ];
    ?>

    <h3>🖼️ c. Kiểm tra Tên Hình ảnh theo Quy tắc:</h3>
    <pre>
<?php
    foreach ($test_images as $image) {
        echo "$image: " . checkImageName($image) . "\n";
    }
?>
    </pre>
</body>
</html>