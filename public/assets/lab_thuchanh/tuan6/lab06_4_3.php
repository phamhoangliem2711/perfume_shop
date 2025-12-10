<?php
// Tắt báo cáo lỗi để code không bị rối khi thực hiện cURL
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

$url = 'https://vnexpress.net/the-thao'; // Sử dụng HTTPS là tốt nhất

// BƯỚC B: SỬ DỤNG CURL THAY CHO file_get_contents()
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Trả về nội dung dưới dạng chuỗi
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Xử lý chuyển hướng
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Tắt kiểm tra SSL (Chỉ dùng cho môi trường dev)
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0'); 

$content = curl_exec($ch);
curl_close($ch);

if ($content === FALSE) {
    die("<h3>⚠️ Lỗi: Không thể tải nội dung. Hãy kiểm tra cấu hình hoặc kết nối.</h3>");
}

// BƯỚC C: IN KẾT QUẢ VÀ PHÂN TÍCH CẤU TRÚC
echo "<h2>1. Kết quả Đọc dữ liệu HTML từ $url (BƯỚC C)</h2>";
echo "<textarea style='width: 100%; height: 200px;'>";
echo htmlspecialchars($content); 
echo "</textarea>";
// Phân tích cấu trúc: Nội dung là toàn bộ mã nguồn HTML. Ta cần tìm các khối dữ liệu bằng class 'title_news'.

// BƯỚC D: LỌC VÀ IN DỮ LIỆU THÔ (Bằng preg_match_all)
$pattern_div = '/<div class="title_news">(.*?)<\/div>/imsU';
preg_match_all($pattern_div, $content, $arr_divs);

echo "<h2>2. Dữ liệu thô đã lọc theo &lt;div class=\"title_news\"&gt; (BƯỚC D)</h2>";
echo "<pre>";
print_r($arr_divs[0]);
echo "</pre>";

// BƯỚC E: DUYỆT MẢNG VÀ HIỂN THỊ DỮ LIỆU TRONG TABLE
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Trích xuất Dữ liệu Thể thao</title>
    <style>
        table { width: 80%; border-collapse: collapse; margin: 20px auto; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #3f51b5; color: white; }
    </style>
</head>
<body>
    <h2>3. Dữ liệu trích lọc chi tiết (BƯỚC E)</h2>
    <table>
        <tr>
            <th>STT</th>
            <th>Tiêu đề Bài viết</th>
            <th>Link (URL)</th>
        </tr>
        <?php
        $stt = 0;
        
        // Pattern để trích xuất Link (href) và Tiêu đề (Nội dung) bên trong mỗi DIV
        $pattern_data = '/<a[^>]*href=["\'](.*?)["\'][^>]*>(.*?)<\/a>/imsU';

        // Duyệt qua nội dung của các DIV đã lọc (nhóm 1 của $arr_divs)
        if (!empty($arr_divs[1])) {
            foreach ($arr_divs[1] as $div_content) {
                // Áp dụng pattern để lấy link và tiêu đề từ thẻ <a>
                preg_match($pattern_data, $div_content, $matches);
                
                if (count($matches) >= 3) {
                    $stt++;
                    $link = $matches[1];
                    // Dùng strip_tags để loại bỏ các thẻ HTML phụ (như <img>, <span class="time">)
                    $title = strip_tags(trim($matches[2])); 
                    
                    echo "<tr>";
                    echo "<td>$stt</td>";
                    echo "<td><strong>$title</strong></td>";
                    echo "<td><a href=\"$link\" target=\"_blank\">Xem chi tiết</a></td>";
                    echo "</tr>";
                }
            }
        } else {
            echo "<tr><td colspan='3'>Không tìm thấy dữ liệu. Có thể cấu trúc website đã thay đổi.</td></tr>";
        }
        ?>
    </table>
</body>
</html>