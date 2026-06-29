# SPEC-002 - Learning Activity Engine

## Mục tiêu

Mọi bài học đều được tạo thành từ nhiều **Learning Activities**.

Mỗi Activity là một đơn vị học độc lập.

Ví dụ:

```text
Bài học

↓

Giới thiệu

↓

Minh họa

↓

Ví dụ

↓

Làm thử

↓

Luyện tập

↓

AI giải thích

↓

Tổng kết
```

Ứng dụng chỉ cần biết:

* Activity loại gì
* Dữ liệu của Activity
* Render đúng giao diện

---

# Activity Types

## 1. Introduction

Hiển thị phần mở đầu.

Ví dụ

```text
Tiêu đề

Mục tiêu

Giới thiệu
```

Không có tương tác.

---

## 2. Theory

Hiển thị kiến thức.

Cho phép:

* Text
* Công thức MathJax
* Image
* Table

Không có tương tác.

---

## 3. Illustration

Mục tiêu:

Giải thích bằng hình ảnh.

Ví dụ:

* SVG
* Canvas
* Konva
* Animation

Ví dụ:

Phân số được kéo để quy đồng.

Điểm chạy trên trục số.

Hai vế phương trình di chuyển.

Đây là Activity quan trọng nhất.

---

## 4. AI Explanation

Người học nhập câu hỏi.

Ví dụ

```text
Tại sao phải quy đồng?
```

AI trả lời.

Có lịch sử chat.

---

## 5. Example

Hiển thị ví dụ.

Ví dụ:

```text
Ví dụ

↓

Bước 1

↓

Bước 2

↓

Bước 3
```

Có nút:

Hiện từng bước.

---

## 6. Guided Practice

Làm bài có hướng dẫn.

Ví dụ

```
2x+5=17
```

Nếu sai

AI Hint.

Nếu đúng

Qua bước tiếp.

---

## 7. Quiz

Các loại

* Single Choice
* Multiple Choice
* True/False

Hiển thị

Question

Options

Submit

Result

---

## 8. Fill Answer

Học sinh nhập đáp án.

Ví dụ

```
3/5+2/5=
```

Có thể nhập

```
1
```

hoặc

```
5/5
```

AI kiểm tra.

---

## 9. Ordering

Sắp xếp thứ tự.

Ví dụ

```
Quy đồng

↓

Cộng tử

↓

Rút gọn
```

Drag & Drop.

---

## 10. Matching

Ghép.

Ví dụ

```
Phân số

↓

Khái niệm
```

---

## 11. Drawing

Canvas.

Ví dụ

* Vẽ tam giác

* Đánh dấu góc

* Nối điểm

---

## 12. Reflection

Cuối bài.

Ví dụ

```text
Con thấy phần nào khó?

○ Quy đồng

○ So sánh

○ Cộng
```

Lưu vào DB.

---

## 13. Summary

Tóm tắt.

Hiển thị

* Kiến thức chính

* Công thức

* Sai thường gặp

---

# Activity Structure

Mọi Activity đều có cùng schema.

```json
{
    "id": 15,
    "lesson_id": 3,
    "type": "quiz",
    "title": "...",
    "data": {},
    "sort_order": 5
}
```

Trong đó

```
data
```

là JSON.

---

# Ví dụ

## Theory

```json
{
    "content":"<h3>...</h3>"
}
```

---

## Quiz

```json
{
  "question":"2+2=?",
  "options":[
      "1",
      "2",
      "4",
      "6"
  ],
  "answer":2,
  "explanation":"..."
}
```

---

## Illustration

```json
{
   "animation":"fraction_addition",
   "params":{
       "a":1,
       "b":3,
       "c":1,
       "d":6
   }
}
```

---

# Render Engine

Pseudo code

```php
foreach($lesson->activities as $activity){

    switch($activity->type){

        case "theory":
            renderTheory();

        case "illustration":
            renderIllustration();

        case "quiz":
            renderQuiz();

        case "example":
            renderExample();

        case "guided_practice":
            renderPractice();

        ...
    }

}
```

---

# Folder Structure

```
modules/

    activity/

        theory/

        illustration/

        quiz/

        example/

        guided_practice/

        drawing/

        reflection/

        summary/
```

Mỗi Activity là một module độc lập.

---

# CMS

Trong CMS.

Giáo viên chỉ cần

```
+

Thêm Activity
```

↓

Chọn

```
Theory

Quiz

Illustration

Example

AI

Drawing
```

↓

Nhập nội dung.

Không phải lập trình.

---

# Design Principle

Không xây "Lesson".

Chỉ xây "Activity Engine".

Lesson chỉ là danh sách các Activities được sắp xếp theo thứ tự.

Đây sẽ là nền tảng để mở rộng cho mọi môn học trong tương lai.

---

# Activity Lifecycle

Mỗi Learning Activity phải tuân theo cùng một vòng đời (Lifecycle).

Mục tiêu:

- Chuẩn hóa cách hoạt động của mọi Activity.
- Giúp Render Engine không cần biết chi tiết từng loại Activity.
- Cho phép mở rộng Activity mới mà không cần sửa Engine.

## Lifecycle

```text
Load

↓

Render

↓

Interaction

↓

Validate

↓

Submit

↓

Evaluate

↓

Feedback

↓

Save Progress

↓

Complete


---

## Tôi còn muốn bổ sung thêm một nguyên tắc nữa

Đây là điều tôi thấy rất quan trọng nhưng nhiều hệ thống bỏ qua:

> **Activity phải là Data-driven, không phải Code-driven.**

Nghĩa là Engine chỉ biết có một `QuizActivity`, còn toàn bộ nội dung, luật chơi và cấu hình đều nằm trong JSON của Activity.

Ví dụ sau này bạn muốn thêm:
- Quiz có đếm ngược thời gian.
- Quiz có nhiều đáp án đúng.
- Quiz hiển thị hình ảnh.
- Quiz phát âm thanh.
- Quiz sinh ngẫu nhiên.

Bạn chỉ cần thêm thuộc tính vào `data` của Activity, **không phải sửa Engine**.

Đây cũng là triết lý mà bạn đang áp dụng với CMS của IMA: **hành vi được cấu hình bằng dữ liệu**, không bị "đóng cứng" trong mã nguồn. Theo tôi, đây là nguyên tắc nên được ghi ngay trong SPEC-002 để Claude xây dựng đúng từ đầu.