# SPEC-003 - Learning Studio (AI Content Management System)

**Version:** 1.0

---

# 1. Mục tiêu

Learning Studio là module trong CMS dùng để:

* Quản lý chương trình học.
* Quản lý Knowledge.
* Quản lý Lesson.
* Sinh nội dung bằng AI.
* Review.
* Publish vào Database.

Learning Studio KHÔNG phục vụ học sinh.

Learning Studio chỉ phục vụ người biên soạn.

---

# 2. Triết lý

AI không phải giáo viên.

AI là Content Assistant.

AI không tự Publish.

Con người luôn là người quyết định cuối cùng.

---

# 3. Menu

```text
Learning Studio

Dashboard

Curriculum

Knowledge

Lessons

Question Bank

AI Studio

Review Queue

Publish Center

Prompt Library

Settings
```

---

# 4. Dashboard

Hiển thị nhanh.

```text
Knowledge

Lesson

Activity

Question

Draft

Published

Need Review
```

---

# 5. Curriculum

Quản lý chương trình.

```text
Toán

↓

Lớp 7

↓

Chương

↓

Bài
```

Ví dụ

```text
Toán

Lớp 7

Chương 1

Bài 1

Bài 2

...
```

Curriculum chỉ quản lý cấu trúc.

Không chứa nội dung.

---

# 6. Knowledge Library

Đây là tài sản quan trọng nhất.

Một Knowledge gồm

```text
Tên

Định nghĩa

Giải thích

Ví dụ

Sai thường gặp

Tag

Keyword
```

Ví dụ

```text
Knowledge

Tên

Quy đồng mẫu số

↓

Định nghĩa

↓

Ví dụ

↓

Sai thường gặp
```

Knowledge có thể dùng lại cho nhiều Lesson.

---

# 7. Lesson Manager

Lesson chỉ gồm

```text
Thông tin

↓

Danh sách Activities
```

Ví dụ

```text
Lesson

↓

Introduction

Theory

Illustration

Example

Quiz

Summary
```

Có Preview.

Có Drag & Drop.

---

# 8. Activity Editor

Danh sách

```text
Introduction

Theory

Illustration

Example

Guided Practice

Quiz

Fill Answer

Matching

Ordering

Drawing

Reflection

Summary

AI Chat
```

Mỗi Activity có:

* Title
* Data(JSON)
* Preview
* Generate AI
* Regenerate
* Delete

---

# 9. Question Bank

Toàn bộ câu hỏi.

Có Filter.

```text
Khối

Chương

Lesson

Tag

Độ khó

Trạng thái
```

Có thể tái sử dụng.

---

# 10. AI Studio

Đây là trái tim của hệ thống.

---

## Bước 1

Chọn Lesson

Ví dụ

```text
Toán 7

↓

Chương 1

↓

Bài 3
```

---

## Bước 2

AI sinh Outline.

Ví dụ

```text
Introduction

Theory

Illustration

Example 1

Example 2

Quiz

Summary
```

Người dùng có thể:

* thêm
* xoá
* đổi thứ tự

---

## Bước 3

Generate từng Activity.

Ví dụ

```text
Introduction

[ Generate ]
```

↓

```text
Theory

[ Generate ]
```

↓

```text
Quiz

[ Generate ]
```

Không Generate toàn bộ.

---

## Bước 4

Preview.

---

## Bước 5

Approve.

---

## Bước 6

Publish.

---

# 11. AI Workers

Mỗi Worker chỉ làm một việc.

```text
Outline Worker

Theory Worker

Illustration Worker

Example Worker

Quiz Worker

Hint Worker

Summary Worker

Validator Worker
```

Không Worker nào sinh toàn bộ Lesson.

---

# 12. Prompt Library

Mỗi Worker có Prompt riêng.

Ví dụ

```text
Outline Prompt

Theory Prompt

Quiz Prompt

Example Prompt

Hint Prompt

Summary Prompt
```

Prompt lưu trong DB.

Có Version.

Có thể sửa.

Không cần sửa code.

---

# 13. Validator

Sau khi AI Generate.

Validator kiểm tra.

Ví dụ

```text
Có Theory?

Có Example?

Có Quiz?

Quiz đủ đáp án?

Có Explanation?

Có Hint?

Đúng JSON?

Đúng Schema?
```

Nếu sai.

Reject.

---

# 14. Review Queue

Danh sách Activity chờ duyệt.

Có

```text
Preview

Edit

Approve

Reject

Regenerate
```

---

# 15. Publish Center

Publish sẽ:

```text
Lesson

↓

Activity

↓

Question

↓

Database
```

Publish không gọi AI.

Publish chỉ ghi dữ liệu.

---

# 16. Version

Mọi Activity đều có Version.

Ví dụ

```text
Version 1

Version 2

Version 3
```

Có thể Restore.

---

# 17. Preview

Cho phép xem Lesson giống hệt giao diện học sinh.

Không Publish vẫn xem được.

---

# 18. Database

Learning Studio chỉ cần khoảng:

```text
curriculum

knowledge

lesson

lesson_activity

question

prompt_library

ai_generation_log

activity_version

publish_log
```

---

# 19. Workflow

```text
Curriculum

↓

Knowledge

↓

Lesson

↓

Generate Outline

↓

Review Outline

↓

Generate Activity

↓

Validate

↓

Review

↓

Publish

↓

Student App
```

---

# 20. Design Principles

Không nhập HTML lớn.

Không nhập Lesson nguyên khối.

Lesson chỉ là tập hợp các Activity.

Activity chỉ là dữ liệu.

Engine quyết định cách Render.

AI chỉ sinh dữ liệu.

Con người quyết định Publish.


# 21. Content Blueprint

Đây là ý tưởng mà hiện nay hầu hết các nền tảng giáo dục nhỏ không có.

Thay vì mỗi lần tạo Lesson đều phải suy nghĩ:

Bài này nên có mấy ví dụ? Bao nhiêu câu hỏi? Có minh họa không?

Ta định nghĩa trước Blueprint.

Ví dụ:

Blueprint: Khái niệm mới
├── Introduction
├── Theory
├── Illustration
├── Example
├── Guided Practice
├── Quiz × 5
└── Summary

Hay:

Blueprint: Ôn tập
├── Warm-up Quiz × 3
├── Quiz × 10
├── AI Review
└── Summary

Khi tạo bài học, bạn chỉ cần chọn:

Blueprint = "Khái niệm mới"

AI sẽ biết chính xác phải sinh những Activity nào và theo thứ tự nào.

Điều này giúp:

Các bài học có cấu trúc thống nhất.
Prompt đơn giản hơn.
Giáo viên không phải thiết kế bài học từ đầu mỗi lần.
Sau này chỉ cần cải tiến một Blueprint là hàng trăm bài học mới đều tốt hơn.

Theo tôi, Blueprint nên trở thành một bảng (lesson_blueprint) và là trung tâm của Learning Studio, còn AI chỉ là công cụ thực hiện theo Blueprint đó. Đây sẽ là nền tảng rất mạnh để mở rộng lên toàn bộ chương trình THCS sau này.