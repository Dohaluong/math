-- Master Curriculum Schema (SPEC-004)

CREATE TABLE IF NOT EXISTS mc_module (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(10)  NOT NULL,
    name        VARCHAR(200) NOT NULL,
    chapter_no  TINYINT UNSIGNED NOT NULL,
    chapter_title VARCHAR(200) NOT NULL,
    sort_order  INT NOT NULL DEFAULT 0,
    UNIQUE KEY uq_code (code)
);

CREATE TABLE IF NOT EXISTS mc_concept (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    module_id       INT NOT NULL,
    code            CHAR(3) NOT NULL,
    name            VARCHAR(200) NOT NULL,
    description     TEXT,
    difficulty      TINYINT NOT NULL DEFAULT 1,
    prerequisite_codes JSON,
    status          ENUM('draft','outline','ai_generated','review','published','improved')
                    NOT NULL DEFAULT 'draft',
    lesson_id       INT DEFAULT NULL,
    sort_order      INT NOT NULL DEFAULT 0,
    UNIQUE KEY uq_code (code),
    FOREIGN KEY (module_id) REFERENCES mc_module(id),
    FOREIGN KEY (lesson_id) REFERENCES lesson(id) ON DELETE SET NULL
);
