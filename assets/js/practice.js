/* =============================================
   Practice quiz logic
   ============================================= */
let currentIdx  = 0;
let answers     = {};   // { question_id: 'A'|'B'|'C'|'D' }
let startTime   = Date.now();
let timerInterval;

// ── Init ────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  renderQuestion(currentIdx);
  startTimer();
  updateProgress();
});

// ── Timer ────────────────────────────────────────
function startTimer() {
  timerInterval = setInterval(() => {
    const elapsed = Math.floor((Date.now() - startTime) / 1000);
    const m = String(Math.floor(elapsed / 60)).padStart(2, '0');
    const s = String(elapsed % 60).padStart(2, '0');
    document.getElementById('timer').textContent = m + ':' + s;
  }, 1000);
}

function getStudyTime() {
  return Math.floor((Date.now() - startTime) / 1000);
}

// ── Render ───────────────────────────────────────
function renderQuestion(idx) {
  const q   = QUESTIONS[idx];
  const sel = answers[q.id] || null;
  const opts = [
    { key: 'A', text: q.option_a },
    { key: 'B', text: q.option_b },
    { key: 'C', text: q.option_c },
    { key: 'D', text: q.option_d },
  ];

  let html = `<div class="quiz-card">
    <div class="quiz-question mathjax-content">${q.question}</div>
    <div>`;

  opts.forEach(o => {
    const active = sel === o.key ? 'selected' : '';
    html += `
    <button class="option-btn ${active}" onclick="selectOption(${q.id}, '${o.key}', this)">
      <span class="opt-letter">${o.key}</span>
      <span class="mathjax-content">${o.text}</span>
    </button>`;
  });

  html += `</div></div>`;
  document.getElementById('quiz-container').innerHTML = html;

  // Re-render MathJax
  if (window.MathJax) {
    MathJax.typesetPromise([document.getElementById('quiz-container')]);
  }

  // Update current question number
  document.getElementById('q-current').textContent = idx + 1;

  // Show/hide hint
  document.getElementById('hint-box').classList.add('d-none');

  // Navigation buttons
  document.getElementById('btn-prev').disabled = idx === 0;

  const isLast = idx === QUESTIONS.length - 1;
  document.getElementById('btn-next').classList.toggle('d-none', isLast);
  document.getElementById('btn-submit').classList.toggle('d-none', !isLast);
}

// ── Option selection ─────────────────────────────
function selectOption(questionId, key, btn) {
  answers[questionId] = key;

  // Update button styles
  btn.closest('.quiz-card').querySelectorAll('.option-btn').forEach(b => {
    b.classList.remove('selected');
    b.querySelector('.opt-letter').style.background = '';
    b.querySelector('.opt-letter').style.color = '';
  });
  btn.classList.add('selected');

  updateProgress();
}

// ── Navigation ───────────────────────────────────
function nextQuestion() {
  if (currentIdx < QUESTIONS.length - 1) {
    currentIdx++;
    renderQuestion(currentIdx);
    updateProgress();
  }
}

function prevQuestion() {
  if (currentIdx > 0) {
    currentIdx--;
    renderQuestion(currentIdx);
    updateProgress();
  }
}

function updateProgress() {
  const answered = Object.keys(answers).length;
  const pct      = Math.round(answered / QUESTIONS.length * 100);
  document.getElementById('q-progress').style.width   = pct + '%';
  document.getElementById('q-answered').textContent   = answered + ' đã trả lời';
}

// ── Hint ─────────────────────────────────────────
function toggleHint() {
  const q    = QUESTIONS[currentIdx];
  const box  = document.getElementById('hint-box');
  if (box.classList.contains('d-none')) {
    box.innerHTML = '<i class="bi bi-lightbulb me-1"></i>' + (q.hint || 'Không có gợi ý cho câu này.');
    box.classList.remove('d-none');
    if (window.MathJax) MathJax.typesetPromise([box]);
  } else {
    box.classList.add('d-none');
  }
}

// ── Submit ───────────────────────────────────────
async function submitQuiz() {
  const unanswered = QUESTIONS.filter(q => !answers[q.id]);
  if (unanswered.length > 0) {
    const ok = confirm(`Bạn còn ${unanswered.length} câu chưa trả lời. Vẫn nộp bài?`);
    if (!ok) return;
  }

  clearInterval(timerInterval);

  const payload = {
    lesson_id:  LESSON_ID,
    study_time: getStudyTime(),
    answers: QUESTIONS.map(q => ({
      question_id:     q.id,
      selected_answer: answers[q.id] || 'A',
      is_correct:      answers[q.id] === q.correct_answer ? 1 : 0,
    })),
  };

  // Disable button
  const btn = document.getElementById('btn-submit');
  btn.disabled  = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

  try {
    const res = await fetch(BASE_URL + '/api/save_answers.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(payload),
    });
    const data = await res.json();
    if (data.success) {
      window.location.href = BASE_URL + '/pages/review.php?lesson_id=' + LESSON_ID;
    } else {
      alert('Có lỗi khi lưu kết quả. Vui lòng thử lại.');
      btn.disabled  = false;
      btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Nộp bài';
    }
  } catch (e) {
    alert('Không thể kết nối máy chủ. Vui lòng thử lại.');
    btn.disabled  = false;
    btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Nộp bài';
  }
}
