// keeps working notes in memory on the "New ticket" page - there is no
// ticket id yet, so nothing is sent to the server here. The notes are
// stored as JSON in a hidden field and saved once the ticket itself is.
document.addEventListener('DOMContentLoaded', function () {
  var stagedForm = document.getElementById('staged-notes-form');

  if (!stagedForm) {
    return; // not on the new ticket page
  }

  var list = document.getElementById('notes-list');
  var input = document.getElementById('note-input');
  var hiddenInput = document.getElementById('working-notes-input');

  var notes = [];

  function renderNote(note) {
    var empty = document.getElementById('notes-empty');
    if (empty) {
      empty.remove();
    }

    var item = document.createElement('div');
    item.className = 'note-item';

    var text = document.createElement('p');
    text.className = 'note-text';
    text.textContent = note;

    var time = document.createElement('span');
    time.className = 'note-time';
    time.textContent = 'Pending - saved with the ticket';

    item.appendChild(text);
    item.appendChild(time);
    list.appendChild(item);
  }

  // re-show notes kept after a failed server-side validation (see ticket_new.php)
  try {
    var existing = JSON.parse(hiddenInput.value || '[]');
    if (Array.isArray(existing)) {
      notes = existing;
      notes.forEach(renderNote);
    }
  } catch (e) {
    notes = [];
  }

  stagedForm.addEventListener('submit', function (e) {
    e.preventDefault();

    var note = input.value.trim();
    if (note === '') {
      return;
    }

    notes.push(note);
    renderNote(note);
    hiddenInput.value = JSON.stringify(notes);

    input.value = '';
    list.scrollTop = list.scrollHeight;
  });
});
