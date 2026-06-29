<?php
/**
 * Activity Render Engine
 * Dispatches rendering to the correct module based on activity type.
 *
 * Available in each module render.php:
 *   $activity  — full row from DB
 *   $data      — decoded JSON
 *   $step_idx  — 0-based index in the lesson
 *   $is_last   — bool
 */
require_once __DIR__ . '/../../includes/math_normalizer.php';
function render_activity(array $activity, int $step_idx, bool $is_last, int $lesson_id = 0): string {
    $type = $activity['type'];
    $data = json_decode($activity['data'], true) ?? [];
    $id   = (int)$activity['id'];

    $module = __DIR__ . '/' . $type . '/render.php';
    if (!file_exists($module)) {
        return '<div class="alert alert-warning">Activity type "' . htmlspecialchars($type) . '" not found.</div>';
    }

    ob_start();
    include $module;
    return ob_get_clean();
}
