<?php
$questions = [
    [
        'id' => 1,
        'question_text' => 'Ngôn ngữ lập trình PHP được phát triển lần đầu tiên bởi ai?',
        'options' => [
            'a' => 'Bill Gates',
            'b' => 'Rasmus Lerdorf',
            'c' => 'Mark Zuckerberg',
            'd' => 'Larry Page'
        ],
        'correct_answer' => 'b'
    ],
    [
        'id' => 2,
        'question_text' => 'abc...? ',
        'options' => [
            'a' => 'b',
            'b' => 'd',
            'c' => 'a',
            'd' => 'c'
        ],
        'correct_answer' => 'b'
    ],
    [
        'id' => 3,
        'question_text' => 'A x a = ?',
        'options' => [
            'a' => 'aa',
            'b' => 'Aa',
            'c' => 'AA',
            'd' => 'Aaa'
        ],
        'correct_answer' => 'b'
    ],
    [
        'id' => 4,
        'question_text' => 'Aa x aa = ?',
        'options' => [
            'a' => 'aaaa',
            'b' => 'Aaaa',
            'c' => 'AAaa',
            'd' => 'AaAA'
        ],
        'correct_answer' => 'b'
    ],
];

// Tổng số câu hỏi có sẵn
$total_questions = count($questions);

// Số câu hỏi ngẫu nhiên cần lấy
$m = rand(2, $total_questions - 1);  // Lấy một số ngẫu nhiên từ 2 đến tổng số câu hỏi - 1

// Kiểm tra nếu số câu hỏi ngẫu nhiên cần lấy không vượt quá tổng số câu hỏi
if ($m >= $total_questions) {
    echo "Số câu hỏi cần lấy phải nhỏ hơn tổng số câu hỏi có sẵn!";
    exit;
}

// Lấy ngẫu nhiên các câu hỏi từ mảng
$random_key = array_rand($questions, $m);  // Lấy ngẫu nhiên các chỉ số câu hỏi
$random_question = [];

// Nếu chỉ lấy một câu hỏi
if ($m == 1) {
    $random_question[] = $questions[$random_key];  // Thêm câu hỏi vào mảng kết quả
} else {
    // Nếu lấy nhiều câu hỏi, duyệt qua các chỉ số ngẫu nhiên và thêm vào mảng
    foreach ($random_key as $key) {
        $random_question[] = $questions[$key];  // Thêm câu hỏi vào mảng kết quả
    }
}

// HTML và hiển thị các câu hỏi ngẫu nhiên
$stt = 1;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đề Thi Trắc Nghiệm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        .question {
            margin-bottom: 20px;
        }
        .question p {
            font-weight: bold;
        }
        .options {
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <h2>Đề Thi Trắc Nghiệm</h2>
    <form action="" method="post">
        <?php foreach ($random_question as $question): ?>
            <div class="question">
                <p><?php echo $stt++ . '. ' . $question['question_text']; ?></p>
                <div class="options">
                    <?php foreach ($question['options'] as $option_key => $option_value): ?>
                        <label>
                            <input type="radio" name="question_<?php echo $question['id']; ?>" value="<?php echo $option_key; ?>"> 
                            <?php echo $option_key . ": " . $option_value; ?>
                        </label><br>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <div style="text-align: center;">
            <input type="submit" value="Nộp bài">
        </div>
    </form>
</body>
</html>
