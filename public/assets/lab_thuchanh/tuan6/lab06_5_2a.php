<?php
    // Tắt báo cáo lỗi (Tùy chọn, để ẩn các lỗi nhỏ từ DOM)
    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE); 

    header("Content-Type: text/html; charset=UTF-8");
    
    // SỬA LỖI 1: Cập nhật URL từ HTTP cũ sang HTTPS mới
    $url = 'https://zingnews.vn/'; 

    // Sử dụng cURL để tải trang
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");

    // CỦNG CỐ KẾT NỐI HTTPS
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Cho phép chuyển hướng
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Bỏ qua xác minh SSL

    $html = curl_exec($ch);
    $curl_error = curl_error($ch); // Lấy lỗi cURL để debug
    curl_close($ch);

    // Kiểm tra nếu lấy nội dung thành công (SỬA LỖI 2: Xử lý lỗi cURL chi tiết hơn)
    if ($html === false || !empty($curl_error)) {
        die("❌ Lỗi: Không thể tải trang <em>$url</em>. Chi tiết lỗi cURL: " . $curl_error);
    } else {
        echo "✔️ Tải thành công trang: $url<br>";
    }

    // Tạo đối tượng DOMDocument để phân tích HTML
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    // Sử dụng @ để bỏ qua lỗi HTML không chuẩn
    @$doc->loadHTML($html);
    libxml_clear_errors();

    // Tìm các tiêu đề tin tức
    $xpath = new DOMXPath($doc);

    // SỬA LỖI 3: Cập nhật XPath chính xác cho Zing News (Tìm các link nằm trong h2/h3 của khối article)
    $xpath_query = '//article//h2/a | //article//h3/a'; 
    $nodes = $xpath->query($xpath_query);

    echo "<h2>Tiêu đề tin trang Zing News:</h2>";
    if ($nodes->length > 0) {
        foreach ($nodes as $node) {
            $title = trim($node->nodeValue);
            
            // Khai báo kiểu DOMElement giúp IDE nhận diện thuộc tính (Không bắt buộc)
            /** @var DOMelement $node */ 
            $link = $node->getAttribute('href');
            
            // Xử lý link tương đối (nếu link không có http/https, thêm base URL)
            if (strpos($link, 'http') === false && strpos($link, '//') === false) {
                 $link = 'https://zingnews.vn/' . ltrim($link, '/');
            }
            
            // Chỉ in tiêu đề không rỗng và có độ dài hợp lý
            if (!empty($title) && strlen($title) > 10) {
                 echo "<p>• <a href='$link' target='_blank'>" . htmlspecialchars($title) . "</a></p>";
            }
        }
    } else {
        echo "<p>❌ Không tìm thấy tiêu đề nào. Kiểm tra lại XPath hoặc cấu trúc HTML.</p>";
    }
?>