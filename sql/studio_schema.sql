-- Learning Studio Schema (SPEC-003)
-- Run after schema.sql and activity_schema.sql

-- Draft activities (before publish to live)
CREATE TABLE IF NOT EXISTS ls_draft (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id   INT NOT NULL,
    type        VARCHAR(50)  NOT NULL,
    title       VARCHAR(255) NOT NULL DEFAULT '',
    data        JSON,
    sort_order  INT NOT NULL DEFAULT 0,
    status      ENUM('draft','approved','rejected','published') NOT NULL DEFAULT 'draft',
    notes       TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lesson(id) ON DELETE CASCADE
);

-- AI Worker prompt library (one active prompt per worker)
CREATE TABLE IF NOT EXISTS ls_prompt (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    worker          VARCHAR(50)  NOT NULL,
    name            VARCHAR(100) NOT NULL,
    system_prompt   TEXT NOT NULL,
    user_template   TEXT NOT NULL,
    output_example  TEXT,
    version         INT NOT NULL DEFAULT 1,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_worker (worker)
);

-- AI generation log
CREATE TABLE IF NOT EXISTS ls_generation_log (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    draft_id        INT,
    lesson_id       INT NOT NULL,
    worker          VARCHAR(50) NOT NULL,
    tokens_input    INT,
    tokens_output   INT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Lesson blueprints (activity structure templates)
CREATE TABLE IF NOT EXISTS ls_blueprint (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    structure   JSON NOT NULL,
    is_default  TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
