USE math_tutor;

CREATE TABLE IF NOT EXISTS activity (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id  INT          NOT NULL,
    type       VARCHAR(50)  NOT NULL,
    title      VARCHAR(200),
    data       JSON         NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (lesson_id) REFERENCES lesson(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS activity_progress (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    student_id   INT              NOT NULL,
    activity_id  INT              NOT NULL,
    is_complete  TINYINT(1)       NOT NULL DEFAULT 0,
    response     JSON,
    is_correct   TINYINT(1),
    attempts     TINYINT UNSIGNED NOT NULL DEFAULT 0,
    completed_at DATETIME,
    UNIQUE KEY uq_student_activity (student_id, activity_id),
    FOREIGN KEY (student_id)  REFERENCES student(id),
    FOREIGN KEY (activity_id) REFERENCES activity(id) ON DELETE CASCADE
);
