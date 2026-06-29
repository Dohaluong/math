# SPEC-005 - Math Content & Formula Standard

**Version:** 1.0

**Status:** Required

---

# 1. Mục tiêu

Chuẩn hóa toàn bộ cách AI sinh, lưu trữ và hiển thị công thức toán học.

Mục tiêu:

* Không lỗi hiển thị.
* Không phụ thuộc vào AI.
* Dễ Render bằng MathJax.
* Dễ chỉnh sửa.
* Dễ mở rộng.

---

# 2. Design Principles

## Single Source of Truth

Toàn bộ công thức toán học chỉ tồn tại dưới **một định dạng duy nhất**.

Định dạng chuẩn là:

> **LaTeX**

Không sử dụng nhiều định dạng khác nhau.

---

# 3. Quy định

## ĐÚNG

```text
\sqrt{2}

\frac{a}{b}

x^2+y^2=z^2

\pi

\sin(x)
```

---

## KHÔNG ĐÚNG

```text
√2

½

π

x²

$

$$

<math>

<img>

SVG Formula
```

---

# 4. Database Standard

Database chỉ lưu biểu thức.

Không lưu ký hiệu render.

Ví dụ:

ĐÚNG

```text
\sqrt{2}
```

ĐÚNG

```text
\frac{a+b}{2}
```

SAI

```text
$\sqrt{2}$
```

SAI

```text
$$\sqrt{2}$$
```

SAI

```text
\(
\sqrt2
\)
```

Renderer sẽ tự thêm dấu bao ngoài.

---

# 5. Formula Types

Có bốn loại công thức.

## Inline Formula

Ví dụ:

```text
\sqrt{2}
```

Hiển thị:

```html
\( \sqrt{2} \)
```

---

## Display Formula

Ví dụ:

```text
\frac{a+b}{2}
```

Hiển thị:

```html
\[
\frac{a+b}{2}
\]
```

---

## Equation

Ví dụ

```text
2x+3=11
```

---

## Matrix

Ví dụ

```latex
\begin{bmatrix}

1&2

3&4

\end{bmatrix}
```

---

# 6. AI Output Rules

AI không được sinh HTML.

AI không được sinh MathJax.

AI chỉ sinh dữ liệu.

Ví dụ:

ĐÚNG

```json
{
    "type":"formula",
    "value":"\\sqrt{2}"
}
```

SAI

```html
<p>$\sqrt2$</p>
```

---

# 7. Content Structure

Không trộn Text và Formula.

ĐÚNG

```json
{
  "type":"paragraph",
  "children":[

      {
          "type":"text",
          "value":"Số vô tỉ là "
      },

      {
          "type":"formula",
          "value":"\\sqrt{2}"
      },

      {
          "type":"text",
          "value":" và "
      },

      {
          "type":"formula",
          "value":"\\pi"
      }

  ]
}
```

Không lưu HTML lớn.

---

# 8. Math Engine

Mọi dữ liệu AI sinh ra phải đi qua Math Engine.

Workflow

```text
AI

↓

Formula Validator

↓

Formula Normalizer

↓

Database

↓

Renderer

↓

MathJax
```

Không được Render trực tiếp nội dung AI.

---

# 9. Formula Validator

Validator kiểm tra:

* Có phải LaTeX?
* Có cú pháp hợp lệ?
* Có đóng mở ngoặc đúng?
* Có lệnh không hỗ trợ?
* Có ký tự Unicode không mong muốn?

Nếu lỗi.

Reject.

---

# 10. Formula Normalizer

Normalizer chuẩn hóa biểu thức.

Ví dụ

Input

```text
√2
```

Output

```text
\sqrt{2}
```

---

Input

```text
π
```

Output

```text
\pi
```

---

Input

```text
x²
```

Output

```text
x^2
```

---

Input

```text
1/2
```

Output

```text
\frac{1}{2}
```

Nếu không thể chuẩn hóa.

Trả về lỗi để Review.

---

# 11. Renderer

Renderer quyết định cách hiển thị.

Ví dụ

Inline

```html
\(formula\)
```

Block

```html
\[
formula
\]
```

Database không chứa các ký hiệu này.

---

# 12. AI Prompt Rules

Prompt của AI phải luôn chứa quy định sau.

```text
Mọi công thức toán học phải sử dụng LaTeX.

Không sử dụng:

$

$$

Unicode Formula

HTML

MathML

SVG

Không thêm ký hiệu render.

Chỉ trả về phần biểu thức.

Ví dụ đúng:

\sqrt{2}

\frac{a}{b}

x^2

Ví dụ sai:

√2

$\sqrt2$

$$\sqrt2$$

x²

π
```

---

# 13. CMS Editor

Editor chỉ lưu dữ liệu.

Editor không render HTML.

Có Preview riêng.

Preview sử dụng MathJax.

---

# 14. Database

Ví dụ

```json
{
    "type":"formula",
    "value":"\\frac{a+b}{2}"
}
```

Không lưu HTML.

Không lưu MathJax.

---

# 15. Future Extension

Math Engine phải có khả năng mở rộng.

Ví dụ

* Chemistry Formula
* Physics Formula
* Matrix
* Graph
* Geometry
* Chemical Equation

Không cần thay đổi Database.

Chỉ bổ sung Formula Type và Renderer.

---

# Core Principles

* LaTeX là định dạng duy nhất cho công thức toán học.
* HTML chỉ là lớp hiển thị.
* Database chỉ lưu dữ liệu.
* AI chỉ sinh dữ liệu.
* Math Engine chịu trách nhiệm kiểm tra, chuẩn hóa và render.
* Không bao giờ render trực tiếp nội dung AI sinh ra.
