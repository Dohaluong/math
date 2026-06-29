/* ================================================================
   Activity Engine — SPEC-002
   Data-driven: all behavior configured by ACTIVITIES[].data
   ================================================================ */

const engine = {
    steps:     null,
    dots:      null,
    current:   0,
    total:     0,
    typeLabels: {
        introduction:     'Mở đầu',
        theory:           'Kiến thức',
        example:          'Ví dụ',
        ai_explanation:   'Hỏi AI',
        quiz:             'Kiểm tra nhanh',
        guided_practice:  'Luyện tập có hướng dẫn',
        fill_answer:      'Điền đáp án',
        reflection:       'Nhìn lại',
        summary:          'Tóm tắt',
    },

    init() {
        this.steps = document.querySelectorAll('.activity-step');
        this.dots  = document.querySelectorAll('.act-dot');
        this.total = this.steps.length;
        // If all activities already completed, jump straight to completion screen
        if (START_IDX >= this.total && this.total > 0) {
            this.finish();
            return;
        }
        this.show(START_IDX);
    },

    show(idx) {
        if (idx < 0 || idx >= this.total) return;
        this.steps.forEach((s, i) => s.style.display = i === idx ? 'block' : 'none');
        this.current = idx;
        this.dots.forEach((d, i) => d.classList.toggle('act-dot--current', i === idx));

        // Progress: show (idx+1)/total so the last step shows 100%
        const pct = Math.round(((idx + 1) / this.total) * 100);
        document.getElementById('act-progress-bar').style.width = pct + '%';
        document.getElementById('act-label').textContent =
            'Hoạt động ' + (idx + 1) + ' / ' + this.total;
        document.getElementById('act-type-label').textContent =
            this.typeLabels[ACTIVITIES[idx]?.type] ?? '';

        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Re-render MathJax for the newly visible step.
        // Use startup.promise so we wait for async load before typesetting.
        const _step = this.steps[idx];
        if (window.MathJax) {
            (MathJax.startup?.promise ?? Promise.resolve())
                .then(() => MathJax.typesetPromise([_step]))
                .catch(() => {});
        }
    },

    next() {
        this._autoSaveCurrent();
        const nextIdx = this.current + 1;
        if (nextIdx < this.total) {
            this.show(nextIdx);
        } else {
            this.finish();
        }
    },

    finish() {
        this._autoSaveCurrent();
        // Set progress bar to 100%
        const bar = document.getElementById('act-progress-bar');
        if (bar) bar.style.width = '100%';
        document.getElementById('activity-wrapper').style.display = 'none';
        document.getElementById('completion-screen').classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        // Record lesson completion in progress table
        fetch(BASE_URL + '/api/activity/complete_lesson.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ lesson_id: LESSON_ID }),
        }).catch(() => {});
    },

    // Save current step if not already marked done (covers introduction, theory, summary, ai_explanation)
    _autoSaveCurrent() {
        const dot = this.dots[this.current];
        if (dot && dot.classList.contains('act-dot--done')) return;
        const actId = parseInt(this.steps[this.current]?.dataset?.activityId);
        if (!actId) return;
        this.markDone(this.current);
        this.save(actId, null, null);
    },

    // Mark a dot as done
    markDone(idx) {
        if (this.dots[idx]) this.dots[idx].classList.add('act-dot--done');
    },

    // Save progress to API (fire-and-forget)
    save(activityId, isCorrect, response) {
        fetch(BASE_URL + '/api/activity/submit.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                activity_id: activityId,
                is_complete: 1,
                is_correct:  isCorrect,
                response:    response,
            }),
        }).catch(() => {});
    },
};

document.addEventListener('DOMContentLoaded', () => engine.init());

async function resetLesson(lessonId) {
    if (!confirm('Xoá kết quả và học lại từ đầu?')) return;
    await fetch(BASE_URL + '/api/activity/reset_lesson.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ lesson_id: lessonId }),
    }).catch(() => {});
    window.location.reload();
}


/* ================================================================
   Answer normalizer — strips LaTeX so students can type plain text
   e.g. \frac{3}{10} and 3/10 both → "3/10"
   ================================================================ */
function normalizeAnswer(str) {
    if (!str && str !== 0) return '';
    return String(str)
        .toLowerCase()
        .replace(/\\frac\{([^}]+)\}\{([^}]+)\}/g, '$1/$2')
        .replace(/\\sqrt\{([^}]+)\}/g, 'sqrt($1)')
        .replace(/\\left|\\right/g, '')
        .replace(/\\cdot|\\times/g, 'x')
        .replace(/\\[a-zA-Z]+/g, '')
        .replace(/[{}]/g, '')
        .replace(/[×·*]/g, 'x')
        .replace(/,/g, '.')
        .replace(/\s+/g, '');
}


/* ================================================================
   Quiz interaction
   ================================================================ */
function quizSelect(actId, selectedIdx, correctIdx, btn) {
    const container = btn.closest('.act-quiz');
    // Prevent re-answering
    if (container.dataset.answered) return;
    container.dataset.answered = '1';

    // Style options
    container.querySelectorAll('.act-option').forEach((b, i) => {
        b.disabled = true;
        if (i === correctIdx) b.classList.add('act-option--correct');
        else if (i === selectedIdx) b.classList.add('act-option--wrong');
    });

    const isCorrect = selectedIdx === correctIdx;
    const feedback  = document.getElementById('feedback-' + actId);
    const nextBtn   = document.getElementById('next-btn-' + actId);
    const expVar    = window['quizExplanation_' + actId] ?? '';

    feedback.classList.remove('d-none');
    const expHtml = wrapBareLatex(expVar);
    if (isCorrect) {
        feedback.innerHTML = '<div class="act-feedback--correct"><i class="bi bi-check-circle-fill me-2"></i>Chính xác! ' + expHtml + '</div>';
    } else {
        feedback.innerHTML = '<div class="act-feedback--wrong"><i class="bi bi-x-circle-fill me-2"></i>Chưa đúng. ' + expHtml + '</div>';
    }
    if (window.MathJax) MathJax.typesetPromise([feedback]).catch(() => {});

    nextBtn.classList.remove('d-none');
    engine.markDone(engine.current);
    engine.save(actId, isCorrect ? 1 : 0, { selected: selectedIdx });
}


/* ================================================================
   Example step reveal
   ================================================================ */
const exampleState = {};

function exampleNext(actId, totalSteps, stepIdx) {
    if (!exampleState[actId]) exampleState[actId] = 0;
    const cur  = exampleState[actId];
    const next = cur + 1;

    if (next < totalSteps) {
        document.getElementById('step-' + actId + '-' + next).style.display = 'block';
        if (window.MathJax) MathJax.typesetPromise([document.getElementById('step-' + actId + '-' + next)]).catch(() => {});
        exampleState[actId] = next;
        document.getElementById('step-counter-' + actId).textContent =
            'Bước ' + (next + 1) + ' / ' + totalSteps;

        if (next === totalSteps - 1) {
            // All steps shown — switch button to "Tiếp theo"
            const btn = document.getElementById('btn-step-' + actId);
            btn.textContent = 'Tiếp theo';
            btn.classList.replace('btn-outline-primary', 'btn-primary');
            btn.onclick = () => { engine.markDone(stepIdx); engine.save(actId, null, null); engine.next(); };
        }
    }
}


/* ================================================================
   Fill Answer validation
   ================================================================ */
const fillAttempts = {};
const MAX_FILL_ATTEMPTS = 3;

function fillSubmit(actId) {
    const input    = document.getElementById('fill-input-' + actId);
    const raw      = input.value.trim();
    if (!raw) return;

    const accepted    = window['fillAccepted_' + actId]    ?? [];
    const explanation = window['fillExplanation_' + actId] ?? '';
    const normalised  = normalizeAnswer(raw);
    const isCorrect   = accepted.some(a => normalizeAnswer(a) === normalised);

    const feedback = document.getElementById('fill-feedback-' + actId);
    feedback.classList.remove('d-none');
    const nextBtn = document.getElementById('next-btn-' + actId);

    if (isCorrect) {
        feedback.innerHTML = '<div class="act-feedback--correct"><i class="bi bi-check-circle-fill me-2"></i>Đúng rồi! ' + wrapBareLatex(explanation) + '</div>';
        nextBtn.classList.remove('d-none');
        input.disabled = true;
        engine.markDone(engine.current);
        engine.save(actId, 1, { answer: raw });
    } else {
        fillAttempts[actId] = (fillAttempts[actId] ?? 0) + 1;
        const attempts   = fillAttempts[actId];
        const remaining  = MAX_FILL_ATTEMPTS - attempts;

        if (attempts >= MAX_FILL_ATTEMPTS) {
            const correct = accepted[0] ?? '';
            feedback.innerHTML = '<div class="act-feedback--wrong">'
                + '<i class="bi bi-x-circle-fill me-2"></i>Chưa đúng. '
                + '<strong>Đáp án đúng: ' + wrapBareLatex(correct) + '</strong>'
                + (explanation ? '. ' + wrapBareLatex(explanation) : '')
                + '</div>';
            input.disabled = true;
            nextBtn.classList.remove('d-none');
            engine.markDone(engine.current);
            engine.save(actId, 0, { answer: raw });
        } else {
            feedback.innerHTML = '<div class="act-feedback--wrong">'
                + '<i class="bi bi-x-circle-fill me-2"></i>Chưa đúng. Hãy thử lại.'
                + '<span class="ms-2 small text-muted">(' + remaining + ' lần thử còn lại)</span>'
                + '</div>';
        }
    }
    if (window.MathJax) MathJax.typesetPromise([feedback]).catch(() => {});
}


/* ================================================================
   Guided Practice step-by-step
   ================================================================ */
const guidedState = {};
const MAX_GUIDED_ATTEMPTS = 3;

function guidedAdvance(actId, stepIdx, totalSteps) {
    const nextStep = stepIdx + 1;
    if (nextStep < totalSteps) {
        setTimeout(() => {
            const nextEl = document.getElementById('gstep-' + actId + '-' + nextStep);
            nextEl.classList.remove('d-none');
            const nextInput = document.getElementById('ginput-' + actId + '-' + nextStep);
            if (nextInput) nextInput.focus();
            if (window.MathJax) MathJax.typesetPromise([nextEl]).catch(() => {});
        }, 600);
    } else {
        setTimeout(() => {
            document.getElementById('guided-complete-' + actId).classList.remove('d-none');
            document.getElementById('next-btn-' + actId).classList.remove('d-none');
            engine.markDone(engine.current);
            engine.save(actId, 1, null);
        }, 600);
    }
}

function guidedSubmit(actId, stepIdx, totalSteps) {
    const input    = document.getElementById('ginput-' + actId + '-' + stepIdx);
    const raw      = input.value.trim();
    if (!raw) return;

    const steps    = window['guidedSteps_' + actId] ?? [];
    const step     = steps[stepIdx] ?? {};
    const accepted = step.accepted ?? [];
    const normalised = normalizeAnswer(raw);
    const isCorrect  = accepted.some(a => normalizeAnswer(a) === normalised);

    if (!guidedState[actId]) guidedState[actId] = {};

    const feedback = document.getElementById('gfeedback-' + actId + '-' + stepIdx);
    feedback.classList.remove('d-none');

    if (isCorrect) {
        guidedState[actId][stepIdx] = 0;
        feedback.innerHTML = '<div class="act-feedback--correct"><i class="bi bi-check-circle-fill me-2"></i>' + wrapBareLatex(step.explanation || 'Đúng!') + '</div>';
        input.disabled = true;
        if (window.MathJax) MathJax.typesetPromise([feedback]).catch(() => {});
        guidedAdvance(actId, stepIdx, totalSteps);
    } else {
        guidedState[actId][stepIdx] = (guidedState[actId][stepIdx] || 0) + 1;
        const attempts = guidedState[actId][stepIdx];

        if (attempts >= MAX_GUIDED_ATTEMPTS) {
            // 3 wrong attempts: reveal correct answer and let student continue
            const correctAnswer = step.answer ?? (accepted[0] ?? '');
            feedback.innerHTML = '<div class="act-feedback--wrong">'
                + '<i class="bi bi-x-circle-fill me-2"></i>Chưa đúng. '
                + '<strong>Đáp án đúng: ' + wrapBareLatex(correctAnswer) + '</strong>'
                + (step.explanation ? '. ' + wrapBareLatex(step.explanation) : '')
                + '</div>'
                + '<button class="btn btn-outline-primary btn-sm mt-2" onclick="guidedContinueAfterReveal(' + actId + ',' + stepIdx + ',' + totalSteps + ')">'
                + '<i class="bi bi-arrow-right me-1"></i>Tiếp tục</button>';
            input.disabled = true;
        } else {
            const remaining = MAX_GUIDED_ATTEMPTS - attempts;
            feedback.innerHTML = '<div class="act-feedback--wrong"><i class="bi bi-x-circle-fill me-2"></i>Chưa đúng. '
                + '<span id="ai-hint-' + actId + '-' + stepIdx + '">Đang lấy gợi ý...</span>'
                + '<span class="ms-2 small text-muted">(' + remaining + ' lần thử còn lại)</span></div>';

            fetch(BASE_URL + '/api/activity/hint.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ activity_id: actId, step_idx: stepIdx, student_answer: raw }),
            })
            .then(r => r.json())
            .then(d => {
                const el = document.getElementById('ai-hint-' + actId + '-' + stepIdx);
                if (el) { el.innerHTML = wrapBareLatex(d.hint ?? 'Hãy thử lại.'); if (window.MathJax) MathJax.typesetPromise([el]).catch(() => {}); }
            })
            .catch(() => {
                const el = document.getElementById('ai-hint-' + actId + '-' + stepIdx);
                if (el) el.textContent = (step.hint ?? 'Hãy thử lại.');
            });
        }
        if (window.MathJax) MathJax.typesetPromise([feedback]).catch(() => {});
    }
}

function guidedContinueAfterReveal(actId, stepIdx, totalSteps) {
    guidedAdvance(actId, stepIdx, totalSteps);
}


/* ================================================================
   Reflection
   ================================================================ */
function reflectionSelect(actId, optIdx, btn) {
    btn.closest('.act-reflection-options')
       .querySelectorAll('.act-refl-option')
       .forEach(b => b.classList.remove('act-refl-option--selected'));
    btn.classList.add('act-refl-option--selected');

    engine.markDone(engine.current);
    engine.save(actId, null, { selected: optIdx });
    document.getElementById('next-btn-' + actId).classList.remove('d-none');
}


/* ================================================================
   AI Free Chat (ai_explanation activity)
   ================================================================ */
async function aiFreeChat(actId, lessonId) {
    const input    = document.getElementById('ai-input-' + actId);
    const messages = document.getElementById('ai-msgs-' + actId);
    const text     = input.value.trim();
    if (!text) return;

    appendMsg(messages, text, 'user');
    input.value = '';

    const typing = document.createElement('div');
    typing.className = 'ai-typing ms-1';
    typing.textContent = 'AI đang trả lời...';
    messages.appendChild(typing);
    messages.scrollTop = messages.scrollHeight;

    try {
        const res = await fetch(BASE_URL + '/api/ai_chat.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ lesson_id: lessonId, question_id: null, message: text }),
        });
        const data = await res.json();
        typing.remove();
        appendMsg(messages, data.response || 'Có lỗi xảy ra.', 'ai');
    } catch {
        typing.remove();
        appendMsg(messages, 'Không thể kết nối. Vui lòng thử lại.', 'ai');
    }
}

// Wrap bare LaTeX commands (not already inside delimiters) in $...$
// Wrap bare LaTeX commands in mixed text+formula strings.
// Handles nested braces (e.g. \frac{2}{\sqrt{0}}) and leaves existing
// \(...\), \[...\], $...$, $$...$$ delimiters untouched.
function wrapBareLatex(text) {
    let result = '', i = 0;
    while (i < text.length) {
        // Existing \(...\) or \[...\] — copy through
        if (text[i] === '\\' && (text[i+1] === '(' || text[i+1] === '[')) {
            const close = text[i+1] === '(' ? '\\)' : '\\]';
            const end   = text.indexOf(close, i + 2);
            if (end >= 0) { result += text.slice(i, end + 2); i = end + 2; continue; }
        }
        // Existing $$...$$ — copy through
        if (text[i] === '$' && text[i+1] === '$') {
            const end = text.indexOf('$$', i + 2);
            if (end >= 0) { result += text.slice(i, end + 2); i = end + 2; continue; }
        }
        // Existing $...$ — copy through
        if (text[i] === '$') {
            const end = text.indexOf('$', i + 1);
            if (end >= 0) { result += text.slice(i, end + 1); i = end + 1; continue; }
        }
        // Bare LaTeX command \cmd — wrap in \(...\) with proper brace depth
        if (text[i] === '\\' && /[a-zA-Z]/.test(text[i+1] || '')) {
            const start = i;
            i++;
            while (i < text.length && /[a-zA-Z]/.test(text[i])) i++;
            // Consume brace groups with proper nesting
            while (i < text.length && text[i] === '{') {
                let depth = 1; i++;
                while (i < text.length && depth > 0) {
                    if (text[i] === '{') depth++;
                    else if (text[i] === '}') depth--;
                    i++;
                }
            }
            result += '\\(' + text.slice(start, i) + '\\)';
            continue;
        }
        result += text[i++];
    }
    return result;
}

function appendMsg(container, text, role) {
    const wrap = document.createElement('div');
    wrap.className = 'ai-msg ai-msg--' + role;
    if (role === 'ai') {
        const label = document.createElement('div');
        label.className = 'ai-msg__label';
        label.textContent = '🤖 Trợ lý AI';
        wrap.appendChild(label);
    }
    const bubble = document.createElement('div');
    bubble.className = 'ai-msg__bubble mathjax-content';
    const safeText = text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\n/g, '<br>');
    bubble.innerHTML = wrapBareLatex(safeText);
    wrap.appendChild(bubble);
    container.appendChild(wrap);
    container.scrollTop = container.scrollHeight;
    if (window.MathJax) MathJax.typesetPromise([bubble]).catch(() => {});
}
