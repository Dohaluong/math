# SPEC-001 - AI Math Tutor MVP

**Version:** 0.2
**Status:** Complete — Ready to Build
**Goal:** Build a simple AI-powered Math learning web application for Grade 7 students.

---

# 1. Overview

## Objective

Create a lightweight web application that helps a Grade 7 student learn mathematics every day.

The application is **not** an LMS, online classroom or examination platform.

The MVP focuses on one simple learning flow:

> Learn → Practice → Understand mistakes → Save progress

---

# 2. Design Principles

Keep everything as simple as possible.

Priorities:

* Easy to use
* Fast to build
* Easy to extend later

Avoid over-engineering.

If a feature does not directly improve today's learning session, it should not be included in MVP.

---

# 3. Target Users

Current MVP supports:

* 1 Student
* 1 Parent

Multi-user support will be implemented later.

---

# 4. Learning Flow

```
Home

↓

Choose Chapter

↓

Choose Lesson

↓

Read Lesson

↓

Practice (10 Questions)

↓

Review Answers

↓

Ask AI

↓

Save Progress
```

A complete learning session should take approximately **20 minutes**.

---

# 5. Navigation

Only four main pages.

```
Home

Math 7

History

Profile
```

No dashboard.

No statistics page.

No gamification.

---

# 6. Course Structure

```
Math 7

Chapter

    Lesson

        Learning Content

        Practice Questions
```

No complicated taxonomy.

---

# 7. Lesson Structure

Every lesson contains:

```
Title

Introduction

Concept

Example

Practice Questions

Summary
```

Each lesson should remain short and focused.

---

# 8. Structured Content Blocks

Lesson content should **NOT** be stored as one large HTML document.

Instead, split content into ordered blocks.

Example:

```
Lesson

├── Introduction

├── Concept

├── Example

├── Note

├── Exercise

└── Summary
```

Each block contains:

* type
* title (optional)
* content
* display_order

Example:

```
Introduction

Concept

Example

Example

Note

Summary
```

## Benefits

* AI can reference specific sections.
* Easier editing.
* Easier mobile layout.
* Future interactive lessons.
* Cleaner CMS architecture.

---

# 9. Question Structure

Each question contains:

```
Question

Option A

Option B

Option C

Option D

Correct Answer

Hint

Explanation
```

Only multiple-choice questions are required for MVP.

---

# 10. AI Responsibilities

AI should NOT generate questions.

AI only performs three tasks.

## 1. Explain mistakes

Explain why the selected answer is incorrect.

---

## 2. Give hints

Hints should guide the student without revealing the answer immediately.

Example:

Hint 1

"Look at the denominator."

Hint 2

"What must be done before adding fractions?"

---

## 3. Encourage

Provide short feedback.

Example:

```
Great work.

You answered 8 out of 10 correctly.

Most mistakes came from finding common denominators.
```

---

# 11. AI Prompt

```
You are a Grade 7 Mathematics teacher.

Do not reveal the final answer immediately.

Guide students step by step.

If the student is wrong:

- explain briefly
- ask another guiding question
- provide one similar example

Maximum response length: 150 words.

Use simple language suitable for middle school students.
```

---

# 12. Database

Keep the database minimal.

```
lesson

lesson_block

question

student

progress

answer_log

ai_chat
```

---

## lesson

```
id

chapter

title
```

---

## lesson_block

```
id

lesson_id

block_type

title

content

display_order
```

block_type example:

```
introduction

concept

example

note

exercise

summary
```

---

## question

```
id

lesson_id

question

option_a

option_b

option_c

option_d

correct_answer

hint

explanation
```

---

## student

```
id

name
```

---

## progress

```
student_id

lesson_id

score

study_time

last_learning
```

---

## answer_log

```
student_id

question_id

selected_answer

is_correct

created_at
```

---

## ai_chat

```
id

student_id

lesson_id

question_id

prompt

response

created_at
```

---

# 13. Technology Stack

Backend

* PHP
* PDO
* MySQL

Frontend

* Bootstrap 5
* MathJax
* CKEditor (or Summernote)

AI

* OpenAI API

---

# 14. MVP Exclusions

Do NOT build:

* XP
* Coins
* Badges
* Shop
* Ranking
* Leaderboard
* Avatar
* Chat room
* Notifications
* AI-generated questions
* Adaptive learning
* OCR
* Speech recognition

These belong to future versions.

---

# 15. Definition of Done

The MVP is considered complete when a student can:

* Browse lessons.
* Read structured lesson content.
* Answer 10 practice questions.
* Review explanations.
* Ask AI about mistakes.
* Continue learning another day with saved progress.

If these six actions work smoothly, the MVP is successful.

---

# 16. Future Roadmap

Version 0.2

* Mistake classification
* Knowledge graph
* Adaptive learning

Version 0.3

* OCR
* Handwriting recognition
* AI-generated exercises

Version 1.0

* Multi-user
* Parent dashboard
* Teacher CMS
* Full Grade 6–9 curriculum
* Learning analytics

---

# 17. Vietnamese Grade 7 Curriculum (Kết nối tri thức)

Use the **Kết nối tri thức với cuộc sống** textbook edition (most common in 2024–2025).

MVP covers Học kỳ 1 first (Chapters 1–2), then extends to Học kỳ 2.

## Học kỳ 1

```
Chương 1 — Số hữu tỉ và số thực
  Bài 1  — Tập hợp số hữu tỉ
  Bài 2  — Cộng, trừ số hữu tỉ
  Bài 3  — Nhân, chia số hữu tỉ
  Bài 4  — Giá trị tuyệt đối của số hữu tỉ
  Bài 5  — Lũy thừa của số hữu tỉ với số mũ tự nhiên
  Bài 6  — Lũy thừa với số mũ nguyên
  Bài 7  — Tỉ lệ thức
  Bài 8  — Dãy tỉ số bằng nhau
  Bài 9  — Số vô tỉ. Căn bậc hai số học
  Bài 10 — Số thực

Chương 2 — Hình học phẳng
  Bài 11 — Góc ở vị trí đặc biệt
  Bài 12 — Hai đường thẳng song song
  Bài 13 — Tiên đề Euclid. Tính chất hai đường thẳng song song
  Bài 14 — Định lý và chứng minh toán học
```

## Học kỳ 2

```
Chương 3 — Tam giác
  Bài 15 — Tổng các góc của một tam giác
  Bài 16 — Quan hệ giữa góc và cạnh đối diện trong tam giác
  Bài 17 — Trường hợp bằng nhau c.c.c
  Bài 18 — Trường hợp bằng nhau c.g.c
  Bài 19 — Trường hợp bằng nhau g.c.g
  Bài 20 — Tam giác cân. Tam giác đều
  Bài 21 — Đường vuông góc và đường xiên
  Bài 22 — Đường trung trực của đoạn thẳng
  Bài 23 — Tính chất ba đường trung trực của tam giác

Chương 4 — Thống kê và xác suất
  Bài 24 — Thu thập và tổ chức dữ liệu
  Bài 25 — Biểu đồ
  Bài 26 — Số trung bình cộng. Trung vị. Mode
  Bài 27 — Xác suất thực nghiệm

Chương 5 — Biểu thức đại số
  Bài 28 — Biểu thức đại số
  Bài 29 — Đa thức một biến
  Bài 30 — Cộng, trừ đa thức một biến
  Bài 31 — Nhân, chia đa thức một biến
```

MVP seed data: **Chương 1, Bài 1 đến Bài 3** (first 3 lessons fully entered).

---

# 18. Full Database Schema

## lesson

```sql
CREATE TABLE lesson (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    chapter_no    TINYINT UNSIGNED NOT NULL,
    chapter_title VARCHAR(200)     NOT NULL,
    lesson_no     TINYINT UNSIGNED NOT NULL,
    title         VARCHAR(200)     NOT NULL,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## lesson_block

```sql
CREATE TABLE lesson_block (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id     INT          NOT NULL,
    block_type    ENUM('introduction','concept','example','note','exercise','summary') NOT NULL,
    title         VARCHAR(200),
    content       TEXT         NOT NULL,
    display_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (lesson_id) REFERENCES lesson(id) ON DELETE CASCADE
);
```

`content` stores text with LaTeX math notation. Example: `Phân số \(\frac{a}{b}\) với \(b \neq 0\)`.

## question

```sql
CREATE TABLE question (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id      INT          NOT NULL,
    question       TEXT         NOT NULL,
    option_a       TEXT         NOT NULL,
    option_b       TEXT         NOT NULL,
    option_c       TEXT         NOT NULL,
    option_d       TEXT         NOT NULL,
    correct_answer ENUM('A','B','C','D') NOT NULL,
    hint           TEXT,
    explanation    TEXT,
    display_order  TINYINT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (lesson_id) REFERENCES lesson(id) ON DELETE CASCADE
);
```

## student

```sql
CREATE TABLE student (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

MVP: insert one row manually. No registration flow needed.

## progress

```sql
CREATE TABLE progress (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    student_id    INT         NOT NULL,
    lesson_id     INT         NOT NULL,
    score         TINYINT UNSIGNED NOT NULL DEFAULT 0,
    total         TINYINT UNSIGNED NOT NULL DEFAULT 10,
    study_time    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    last_learning DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_student_lesson (student_id, lesson_id),
    FOREIGN KEY (student_id) REFERENCES student(id),
    FOREIGN KEY (lesson_id)  REFERENCES lesson(id)
);
```

`score` = số câu đúng. `study_time` = giây (giới hạn SMALLINT ≈ 18 giờ/session, đủ cho MVP).

## answer_log

```sql
CREATE TABLE answer_log (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT         NOT NULL,
    question_id     INT         NOT NULL,
    selected_answer ENUM('A','B','C','D') NOT NULL,
    is_correct      TINYINT(1)  NOT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id)  REFERENCES student(id),
    FOREIGN KEY (question_id) REFERENCES question(id)
);
```

## ai_chat

```sql
CREATE TABLE ai_chat (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    student_id  INT  NOT NULL,
    lesson_id   INT  NOT NULL,
    question_id INT,
    prompt      TEXT NOT NULL,
    response    TEXT NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES student(id),
    FOREIGN KEY (lesson_id)  REFERENCES lesson(id)
);
```

---

# 19. Folder Structure

```
/Math
├── index.php                  ← redirect to /pages/home.php
├── config.php                 ← DB + OpenAI credentials
├── db.php                     ← PDO singleton
├── .env                       ← OPENAI_API_KEY (never commit)
│
├── pages/
│   ├── home.php               ← Home
│   ├── math7.php              ← Chapter list
│   ├── lesson.php             ← Read lesson (lesson_blocks)
│   ├── practice.php           ← 10-question quiz
│   ├── review.php             ← Review answers + explanations
│   ├── history.php            ← Progress list
│   └── profile.php            ← Student name
│
├── api/
│   ├── save_progress.php      ← POST: save score + study_time
│   ├── save_answers.php       ← POST: bulk insert answer_log
│   └── ai_chat.php            ← POST: send to OpenAI, return response
│
├── assets/
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
│
├── sql/
│   ├── schema.sql             ← CREATE TABLE statements
│   └── seed.sql               ← Sample lessons + questions
│
└── admin/                     ← Simple content entry (MVP only)
    ├── lessons.php
    ├── questions.php
    └── edit_lesson.php
```

---

# 20. URL / Route Map

| URL | Page | Description |
|-----|------|-------------|
| `/Math/` | Home | Ngày học hôm nay, bài tiếp theo |
| `/Math/pages/math7.php` | Math 7 | Danh sách chương và bài |
| `/Math/pages/lesson.php?id=1` | Lesson | Đọc nội dung bài học |
| `/Math/pages/practice.php?lesson_id=1` | Practice | Làm 10 câu trắc nghiệm |
| `/Math/pages/review.php?lesson_id=1` | Review | Xem đáp án và giải thích |
| `/Math/pages/history.php` | History | Lịch sử học |
| `/Math/pages/profile.php` | Profile | Tên học sinh |

---

# 21. OpenAI Integration

## Model

Use `gpt-4o-mini` — fast, cheap, sufficient for 150-word tutoring responses.

## API Call (api/ai_chat.php)

```
POST https://api.openai.com/v1/chat/completions

{
  "model": "gpt-4o-mini",
  "max_tokens": 250,
  "temperature": 0.4,
  "messages": [
    { "role": "system", "content": "<system prompt from Section 11>" },
    { "role": "user",   "content": "<constructed context>" }
  ]
}
```

## User Message Context (constructed server-side)

```
Bài học: {lesson.title}
Câu hỏi: {question.question}
Đáp án đúng: {question.correct_answer}
Học sinh chọn: {selected_answer}
Giải thích trong bài: {question.explanation}

Học sinh đang hỏi: {student_message}
```

Do NOT send the correct answer label in the context — let AI guide, not reveal.
Remove `correct_answer` from the context; only send `explanation` and question text.

## Corrected Context (server-side, safe version)

```
Bài học: {lesson.title}
Câu hỏi: {question.question}
Các lựa chọn: A. {option_a}  B. {option_b}  C. {option_c}  D. {option_d}
Học sinh chọn: {selected_answer} — {option_selected_text}
Gợi ý: {question.hint}

Câu hỏi từ học sinh: {student_message}
```

## API Key Storage

Store in `config.php` loaded from `.env`:

```php
$openai_key = getenv('OPENAI_API_KEY');
```

Add `.env` to `.gitignore`.

---

# 22. Math Formula Handling

## Storage

Store LaTeX in the database using inline and display notation:

```
Inline:   \( ... \)       example: \(\frac{3}{4}\)
Display:  \[ ... \]       example: \[\frac{a}{b} + \frac{c}{d} = \frac{ad+bc}{bd}\]
```

## Rendering

Include MathJax once in the shared layout `<head>`:

```html
<script>
  MathJax = { tex: { inlineMath: [['\\(','\\)']] } };
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"></script>
```

When outputting DB content in PHP, use `htmlspecialchars()` on non-math text but output math blocks raw — or better, store math and text separately if needed. For MVP, trust that DB content is safe (admin-entered only).

---

# 23. Session / Authentication

MVP: **no login required**.

Store `student_id = 1` in PHP session on first visit.

```php
session_start();
if (!isset($_SESSION['student_id'])) {
    $_SESSION['student_id'] = 1;
}
```

This is sufficient for 1 student. Multi-user login is Version 1.0.

---

# 24. Content Entry Strategy

For MVP, content is entered two ways:

1. **seed.sql** — first 3 lessons fully populated via SQL insert.
2. **admin/ pages** — simple HTML forms to add/edit lessons and questions (no auth needed for localhost).

Admin pages are local-only. Do not expose to internet.

CKEditor is NOT needed for MVP. Plain `<textarea>` in admin is sufficient. LaTeX is typed directly.

---

# Core Philosophy

The objective is **not** to build the biggest learning platform.

The objective is to help one student understand mathematics better every day.

Everything else is secondary.
