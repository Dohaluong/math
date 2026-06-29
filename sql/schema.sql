CREATE DATABASE IF NOT EXISTS math_tutor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE math_tutor;

CREATE TABLE lesson (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    chapter_no    TINYINT UNSIGNED NOT NULL,
    chapter_title VARCHAR(200)     NOT NULL,
    lesson_no     TINYINT UNSIGNED NOT NULL,
    title         VARCHAR(200)     NOT NULL,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE lesson_block (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id     INT          NOT NULL,
    block_type    ENUM('introduction','concept','example','note','exercise','summary') NOT NULL,
    title         VARCHAR(200),
    content       TEXT         NOT NULL,
    display_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (lesson_id) REFERENCES lesson(id) ON DELETE CASCADE
);

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

CREATE TABLE student (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE progress (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    student_id    INT              NOT NULL,
    lesson_id     INT              NOT NULL,
    score         TINYINT UNSIGNED NOT NULL DEFAULT 0,
    total         TINYINT UNSIGNED NOT NULL DEFAULT 10,
    study_time    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    last_learning DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_student_lesson (student_id, lesson_id),
    FOREIGN KEY (student_id) REFERENCES student(id),
    FOREIGN KEY (lesson_id)  REFERENCES lesson(id)
);

CREATE TABLE answer_log (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT                      NOT NULL,
    question_id     INT                      NOT NULL,
    selected_answer ENUM('A','B','C','D')    NOT NULL,
    is_correct      TINYINT(1)               NOT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id)  REFERENCES student(id),
    FOREIGN KEY (question_id) REFERENCES question(id)
);

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
