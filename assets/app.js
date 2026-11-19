(function () {
  const STUDENT = /^[0-9]+@upsamail\.edu\.gh$/;
  const STAFF = /^[A-Za-z0-9._-]+@upsa\.edu\.gh$/;
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => [...c.querySelectorAll(s)];

  // ---- Auth page: tabs + validation ----
  const login = $('#loginForm'), reg = $('#registerForm');
  if (login && reg) {
    $$('.tab').forEach(t => t.addEventListener('click', () => {
      $$('.tab').forEach(x => x.classList.toggle('active', x === t));
      login.classList.toggle('hidden', t.dataset.tab !== 'login');
      reg.classList.toggle('hidden', t.dataset.tab !== 'register');
    }));
    const setErr = (id, msg) => {
      $(`[data-for="${id}"]`).textContent = msg;
      $('#' + id).classList.toggle('invalid', !!msg);
      return !msg;
    };
    const emailMsg = (v, allowStaff) =>
      STUDENT.test(v) || (allowStaff && STAFF.test(v)) ? '' :
      'Use your student ID followed by @upsamail.edu.gh, e.g. 10293847@upsamail.edu.gh.';

    login.addEventListener('submit', e => {
      const em = $('#l_email').value.trim().toLowerCase();
      const a = setErr('l_email', emailMsg(em, true));
      const b = setErr('l_pass', $('#l_pass').value ? '' : 'Enter your password.');
      if (!(a && b)) e.preventDefault();
    });
    reg.addEventListener('submit', e => {
      const em = $('#r_email').value.trim().toLowerCase();
      const ok = [
        setErr('r_name', $('#r_name').value.trim() ? '' : 'Enter your full name.'),
        setErr('r_email', emailMsg(em, false)),
        setErr('r_pass', $('#r_pass').value.length >= 8 ? '' : 'Password must be at least 8 characters.'),
        setErr('r_conf', $('#r_conf').value === $('#r_pass').value ? '' : 'Passwords do not match.')
      ].every(Boolean);
      if (!ok) e.preventDefault();
    });
  }

  // ---- Student dashboard: live search + filters ----
  const grid = $('#grid');
  if (grid) {
    const cards = $$('.pq', grid);
    const ctl = { q: $('#search'), dept: $('#f_dept'), sem: $('#f_sem'), year: $('#f_year'), type: $('#f_type') };
    const apply = () => {
      const q = ctl.q.value.trim().toLowerCase();
      let n = 0;
      cards.forEach(c => {
        const d = c.dataset;
        const show = (!q || d.search.includes(q)) &&
          (!ctl.dept.value || d.dept === ctl.dept.value) &&
          (!ctl.sem.value || d.sem === ctl.sem.value) &&
          (!ctl.year.value || d.year === ctl.year.value) &&
          (!ctl.type.value || d.type === ctl.type.value);
        c.classList.toggle('hidden', !show);
        if (show) n++;
      });
      $('#count').textContent = n + (n === 1 ? ' past question' : ' past questions');
      $('#empty').classList.toggle('hidden', n !== 0);
    };
    Object.values(ctl).forEach(el => el.addEventListener('input', apply));
    $('#reset').addEventListener('click', () => { Object.values(ctl).forEach(el => el.value = ''); apply(); });
    apply();
  }

  // ---- Admin: drag and drop ----
  const dz = $('#dropzone');
  if (dz) {
    const input = $('#file'), label = $('#fileName');
    const show = () => { if (input.files[0]) label.textContent = input.files[0].name; };
    input.addEventListener('change', show);
    ['dragenter', 'dragover'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.add('drag'); }));
    ['dragleave', 'drop'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.remove('drag'); }));
    dz.addEventListener('drop', e => { if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; show(); } });
  }
})();
