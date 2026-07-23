// contact.js — custom cursor, reveal animations, form validation, and FAQ accordion
(function(){

  /* ---------- Custom cursor (mouse-only, same pattern as
     rolls-roycemotorcars.com: a small dot plus a larger ring that
     eases behind it and expands over clickable elements) ---------- */
  if (window.matchMedia('(pointer: fine)').matches){
    const dot = document.createElement('div');
    dot.className = 'cursor-dot';
    const ring = document.createElement('div');
    ring.className = 'cursor-ring';
    document.body.appendChild(dot);
    document.body.appendChild(ring);

    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let ringX = mouseX;
    let ringY = mouseY;

    document.addEventListener('mousemove', (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
      dot.style.transform = `translate(${mouseX}px, ${mouseY}px) translate(-50%, -50%)`;
    });

    function animateRing(){
      ringX += (mouseX - ringX) * 0.18;
      ringY += (mouseY - ringY) * 0.18;
      ring.style.transform = `translate(${ringX}px, ${ringY}px) translate(-50%, -50%)`;
      requestAnimationFrame(animateRing);
    }
    animateRing();

    document.addEventListener('mouseleave', () => {
      dot.classList.add('is-hidden');
      ring.classList.add('is-hidden');
    });
    document.addEventListener('mouseenter', () => {
      dot.classList.remove('is-hidden');
      ring.classList.remove('is-hidden');
    });

    const interactiveSelector = 'a, button, input, textarea, .faq-question, .btn, .form-submit';
    document.querySelectorAll(interactiveSelector).forEach(el => {
      el.addEventListener('mouseenter', () => {
        ring.classList.add('is-active');
        dot.classList.add('is-active');
      });
      el.addEventListener('mouseleave', () => {
        ring.classList.remove('is-active');
        dot.classList.remove('is-active');
      });
    });
  }

  /* ---------- Reveal on scroll (same pattern as about.js) ---------- */
  const reveals = document.querySelectorAll('.reveal');
  const revealObs = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  reveals.forEach(el => revealObs.observe(el));

  /* ---------- Field references ---------- */
  const nameInput = document.getElementById('fullName');
  const nameWrap = document.getElementById('nameWrap');
  const nameHint = document.getElementById('nameHint');

  const emailInput = document.getElementById('email');
  const emailWrap = document.getElementById('emailWrap');
  const emailHint = document.getElementById('emailHint');

  const subjectInput = document.getElementById('subject');
  const subjectWrap = document.getElementById('subjectWrap');
  const subjectHint = document.getElementById('subjectHint');

  const messageInput = document.getElementById('message');
  const messageWrap = document.getElementById('messageWrap');
  const messageHint = document.getElementById('messageHint');

  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function shake(el){
    el.style.animation = 'none';
    void el.offsetWidth;
    el.style.animation = 'shakeField 0.4s ease';
  }

  function setField(wrap, hint, message, state){
    // state: 'error' | 'ok'
    wrap.classList.toggle('is-error', state === 'error');
    hint.textContent = message;
    hint.className = state === 'error' ? 'hint error' : 'hint';
  }

  function validate({ showEmpty = false } = {}){
    let ok = true;

    const nVal = nameInput.value.trim();
    if (nVal.length === 0){
      setField(nameWrap, nameHint, showEmpty ? 'Please enter your name' : '', showEmpty ? 'error' : 'ok');
      ok = false;
    } else {
      setField(nameWrap, nameHint, '', 'ok');
    }

    const eVal = emailInput.value.trim();
    if (eVal.length === 0){
      setField(emailWrap, emailHint, showEmpty ? 'Please enter your email' : '', showEmpty ? 'error' : 'ok');
      ok = false;
    } else if (!emailRe.test(eVal)){
      setField(emailWrap, emailHint, 'Enter a valid email address', 'error');
      ok = false;
    } else {
      setField(emailWrap, emailHint, '', 'ok');
    }

    const sVal = subjectInput.value.trim();
    if (sVal.length === 0){
      setField(subjectWrap, subjectHint, showEmpty ? 'Let us know what this is about' : '', showEmpty ? 'error' : 'ok');
      ok = false;
    } else {
      setField(subjectWrap, subjectHint, '', 'ok');
    }

    const mVal = messageInput.value.trim();
    if (mVal.length === 0){
      setField(messageWrap, messageHint, showEmpty ? 'Please add a short message' : '', showEmpty ? 'error' : 'ok');
      ok = false;
    } else if (mVal.length < 10){
      setField(messageWrap, messageHint, 'A few more details would help', 'error');
      ok = false;
    } else {
      setField(messageWrap, messageHint, '', 'ok');
    }

    return ok;
  }

  [nameInput, emailInput, subjectInput, messageInput].forEach(el => {
    el.addEventListener('input', () => validate());
  });

  const form = document.getElementById('contactForm');
  const submitBtn = document.getElementById('submitBtn');
  const btnLabel = document.getElementById('btnLabel');
  const spinner = document.getElementById('spinner');
  const formCard = document.getElementById('formCard');

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const ok = validate({ showEmpty: true });

    if (!ok){
      if (nameInput.value.trim().length === 0) shake(nameWrap);
      if (emailInput.value.trim().length === 0 || !emailRe.test(emailInput.value.trim())) shake(emailWrap);
      if (subjectInput.value.trim().length === 0) shake(subjectWrap);
      if (messageInput.value.trim().length < 10) shake(messageWrap);
      return;
    }

    submitBtn.disabled = true;
    btnLabel.style.display = 'none';
    spinner.style.display = 'inline-block';

    // Simulated send — wire this up to a real endpoint when the backend is ready.
    setTimeout(() => {
      formCard.classList.add('success');
    }, 1100);
  });

  /* ---------- FAQ accordion ---------- */
  document.querySelectorAll('.faq-item').forEach(item => {
    const question = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer');

    question.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');

      document.querySelectorAll('.faq-item.open').forEach(openItem => {
        if (openItem !== item){
          openItem.classList.remove('open');
          openItem.querySelector('.faq-answer').style.maxHeight = null;
        }
      });

      if (isOpen){
        item.classList.remove('open');
        answer.style.maxHeight = null;
      } else {
        item.classList.add('open');
        answer.style.maxHeight = answer.scrollHeight + 'px';
      }
    });
  });

})();