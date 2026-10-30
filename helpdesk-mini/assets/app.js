// finds every status dropdown on the page and wires up a change listener

// "In Progress" -> "status-in-progress", matches the CSS classes
function statusToClass(status) {
  return 'status-' + status.toLowerCase().replace(/\s+/g, '-');
}

document.querySelectorAll('.status-select').forEach(function (select) {
  select.addEventListener('change', function () {
    const ticketId = select.dataset.ticketId;
    const newStatus = select.value;
    const previousClass = statusToClass(select.dataset.currentStatus || newStatus);

    // the little "Saved" / "Error" span right after the select
    const feedback = select.nextElementSibling;

    fetch('status_update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      // sends the session cookie along, so the server knows who we are
      credentials: 'same-origin',
      body: JSON.stringify({ id: ticketId, status: newStatus }),
    })
      .then(function (response) {
        return response.json().then(function (data) {
          return { ok: response.ok, data: data };
        });
      })
      .then(function (result) {
        if (result.ok && result.data.ok) {
          // re-color the pill itself, not just the "Saved" message
          select.classList.remove(previousClass);
          select.classList.add(statusToClass(newStatus));
          select.dataset.currentStatus = newStatus;

          feedback.textContent = 'Saved';
          feedback.classList.remove('status-error');
          feedback.classList.add('status-success');
        } else {
          // failed server-side - revert the dropdown to the saved value
          select.value = select.dataset.currentStatus || select.value;

          feedback.textContent = result.data.error || 'Error';
          feedback.classList.remove('status-success');
          feedback.classList.add('status-error');
        }

        // clear the message after a couple of seconds, like a toast
        setTimeout(function () {
          feedback.textContent = '';
        }, 2000);
      })
      .catch(function () {
        feedback.textContent = 'Network error';
        feedback.classList.add('status-error');
      });
  });
});
