// sends a working note via fetch and appends it to the list, no reload
document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('notes-form');

  if (!form) {
    return; // not on the ticket detail page
  }

  var list = document.getElementById('notes-list');
  var input = document.getElementById('note-input');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    var note = input.value.trim();
    if (note === '') {
      return;
    }

    fetch('ticket_note_add.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ ticket_id: form.dataset.ticketId, note: note }),
    })
      .then(function (response) {
        return response.json().then(function (data) {
          return { ok: response.ok, data: data };
        });
      })
      .then(function (result) {
        if (!result.ok || !result.data.ok) {
          alert(result.data.error || 'Could not save the note.');
          return;
        }

        var empty = document.getElementById('notes-empty');
        if (empty) {
          empty.remove();
        }

        // built with textContent, not innerHTML - same XSS rule as the PHP side
        var item = document.createElement('div');
        item.className = 'note-item';

        var text = document.createElement('p');
        text.className = 'note-text';
        text.textContent = result.data.note.note;

        var time = document.createElement('span');
        time.className = 'note-time';
        time.textContent = result.data.note.created_at;

        item.appendChild(text);
        item.appendChild(time);
        list.appendChild(item);

        list.scrollTop = list.scrollHeight;
        input.value = '';
      })
      .catch(function () {
        alert('Network error - note was not saved.');
      });
  });
});
