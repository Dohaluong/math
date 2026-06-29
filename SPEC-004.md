# SPEC-004 - Master Curriculum (Learning Roadmap)

**Version:** 1.0

**Status:** Draft

---

# 1. Mục tiêu

Master Curriculum là bộ mục lục chuẩn của toàn bộ chương trình học.

Nó là "backlog" để AI và giáo viên lần lượt tạo bài học.

Master Curriculum **không chứa nội dung bài học**.

Nó chỉ định nghĩa:

* Chương trình
* Chương
* Concept
* Skill
* Trạng thái phát triển

---

# 2. Triết lý

Không xây dựng bài học theo SGK.

Không xây dựng theo từng trang sách.

Thay vào đó:

```text
Curriculum

↓

Module

↓

Concept

↓

Skill

↓

Activity

↓

Question
```

Lesson chỉ là cách sắp xếp các Concept thành trải nghiệm học.

---

# 3. Cấu trúc Curriculum

Ví dụ:

```text
Toán

└── Lớp 7

      ├── Chương 1

      ├── Chương 2

      ├── Chương 3

      └── ...
```

---

# 4. Module

## Module 1 - Số hữu tỉ

### Chương 1 - Tập hợp số hữu tỉ

```text
□ 001. Khái niệm số hữu tỉ

□ 002. Biểu diễn số hữu tỉ trên trục số

□ 003. So sánh số hữu tỉ

□ 004. Giá trị tuyệt đối của số hữu tỉ

□ 005. Cộng số hữu tỉ

□ 006. Trừ số hữu tỉ

□ 007. Nhân số hữu tỉ

□ 008. Chia số hữu tỉ

□ 009. Thứ tự thực hiện phép tính

□ 010. Bài toán thực tế về số hữu tỉ
```

---

## Module 2 - Số thực

```text
□ 011. Căn bậc hai số học

□ 012. Số vô tỉ

□ 013. Số thực

□ 014. Làm tròn số

□ 015. Trục số thực
```

---

## Module 3 - Đại số

```text
□ 016. Biểu thức đại số

□ 017. Giá trị biểu thức

□ 018. Đơn thức

□ 019. Đa thức

□ 020. Thu gọn đa thức

□ 021. Cộng đa thức

□ 022. Trừ đa thức

□ 023. Đơn thức đồng dạng

□ 024. Nhân đơn thức với đa thức

□ 025. Nhân đa thức với đa thức
```

---

## Module 4 - Hằng đẳng thức

```text
□ 026. Bình phương tổng

□ 027. Bình phương hiệu

□ 028. Hiệu hai bình phương

□ 029. Vận dụng hằng đẳng thức
```

---

## Module 5 - Phân tích đa thức

```text
□ 030. Đặt nhân tử chung

□ 031. Dùng hằng đẳng thức

□ 032. Nhóm hạng tử

□ 033. Phối hợp các phương pháp
```

---

## Module 6 - Hàm số

```text
□ 034. Đại lượng tỉ lệ thuận

□ 035. Đại lượng tỉ lệ nghịch

□ 036. Hàm số

□ 037. Mặt phẳng tọa độ

□ 038. Đồ thị hàm số
```

---

## Module 7 - Hình học

```text
□ 039. Góc

□ 040. Hai góc đối đỉnh

□ 041. Hai đường thẳng vuông góc

□ 042. Hai đường thẳng song song

□ 043. Tiên đề Euclid

□ 044. Góc tạo bởi cát tuyến

□ 045. Tam giác

□ 046. Tổng ba góc tam giác

□ 047. Tam giác cân

□ 048. Tam giác đều

□ 049. Định lý Pythagore

□ 050. Quan hệ cạnh và góc
```

---

# 5. Concept

Concept là đơn vị kiến thức.

Ví dụ:

```text
Concept

Tên

Mô tả

Điều kiện tiên quyết

Concept liên quan

Độ khó
```

Ví dụ:

```text
Concept

005

Tên

Cộng số hữu tỉ

Prerequisite

So sánh số hữu tỉ

Quy đồng

Related

Trừ số hữu tỉ
```

---

# 6. Skill

Skill là đơn vị đánh giá năng lực.

Một Concept có nhiều Skill.

Ví dụ:

```text
Concept

Cộng số hữu tỉ

↓

Skills

□ Nhận biết dấu

□ Quy đồng mẫu số

□ Cộng tử số

□ Giữ nguyên mẫu số

□ Rút gọn kết quả

□ Kiểm tra đáp án
```

AI sẽ sinh bài tập theo từng Skill.

---

# 7. Activity

Từ mỗi Concept, AI sinh Activity.

Ví dụ:

```text
Introduction

Theory

Illustration

Example

Guided Practice

Quiz

Summary
```

Lesson chỉ là tập hợp các Activity.

---

# 8. Workflow

```text
Curriculum

↓

Concept

↓

Skill

↓

Lesson

↓

Activities

↓

Question

↓

Publish
```

---

# 9. Trạng thái

Mỗi Concept có trạng thái.

```text
Draft

↓

Outline

↓

Knowledge

↓

AI Generated

↓

Review

↓

Published

↓

Improved
```

---

# 10. Dashboard

Hiển thị tiến độ.

Ví dụ:

```text
Toán 7

50 Concept

Completed

28

Remaining

22

56%
```

---

# 11. AI Generation

Khi chọn một Concept.

Ví dụ:

```text
005

Cộng số hữu tỉ
```

AI sẽ sinh:

```text
✓ Introduction

✓ Theory

✓ Illustration

✓ Example 1

✓ Example 2

✓ Guided Practice

✓ Quiz Easy

✓ Quiz Medium

✓ Quiz Hard

✓ Summary
```

Người dùng Review trước khi Publish.

---

# 12. Database

Đề xuất các bảng:

```text
curriculum

module

chapter

concept

skill

lesson

lesson_activity

question

concept_progress
```

---

# 13. Design Principles

* Curriculum là nguồn dữ liệu gốc.
* Concept là đơn vị kiến thức.
* Skill là đơn vị đánh giá năng lực.
* Lesson chỉ là tập hợp các Activity.
* Activity là đơn vị hiển thị.
* Question là đơn vị luyện tập.
* AI sinh nội dung theo Concept và Skill.
* Con người luôn Review trước khi Publish.

---

# 14. Roadmap

## Phase 1

* Hoàn thiện Curriculum.
* Hoàn thiện Concept.
* Hoàn thiện Skill.

## Phase 2

* AI sinh Outline.
* AI sinh Activity.
* Review.

## Phase 3

* Publish.
* Học sinh sử dụng.

## Phase 4

* AI phân tích Skill yếu.
* Adaptive Learning.
* Personalized Learning Path.

---

# Core Philosophy

Không cố gắng tạo hàng nghìn bài học ngay từ đầu.

Hãy xây dựng một hệ thống có cấu trúc rõ ràng:

**Curriculum → Concept → Skill → Activity → Question**

Khi cấu trúc này hoàn chỉnh, AI có thể tạo ra số lượng lớn bài học chất lượng cao, đồng thời dễ dàng mở rộng sang các môn học khác như Vật lý, Hóa học và Tiếng Anh.
