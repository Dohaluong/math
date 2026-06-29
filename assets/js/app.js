/* =============================================
   AI Chat (used on review.php)
   ============================================= */
function toggleAiChat(questionId) {
  const box = document.getElementById('chat-box-' + questionId);
  box.classList.toggle('d-none');
  if (!box.classList.contains('d-none')) {
    document.getElementById('chat-input-' + questionId).focus();
  }
}

async function sendAiMessage(questionId, lessonId) {
  const input    = document.getElementById('chat-input-' + questionId);
  const messages = document.getElementById('chat-msgs-' + questionId);
  const text     = input.value.trim();
  if (!text) return;

  // Append user bubble
  appendMsg(messages, text, 'user');
  input.value = '';

  // Typing indicator
  const typing = document.createElement('div');
  typing.className = 'ai-typing ms-1';
  typing.textContent = 'AI đang trả lời...';
  messages.appendChild(typing);
  messages.scrollTop = messages.scrollHeight;

  try {
    const res = await fetch('/Math/api/ai_chat.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ lesson_id: lessonId, question_id: questionId, message: text }),
    });
    const data = await res.json();
    typing.remove();
    appendMsg(messages, data.response || 'Có lỗi xảy ra.', 'ai');
  } catch (e) {
    typing.remove();
    appendMsg(messages, 'Không thể kết nối. Vui lòng thử lại.', 'ai');
  }
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
  bubble.innerHTML = text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/\n/g, '<br>');
  wrap.appendChild(bubble);
  container.appendChild(wrap);
  container.scrollTop = container.scrollHeight;
  if (window.MathJax) MathJax.typesetPromise([bubble]).catch(() => {});
}
