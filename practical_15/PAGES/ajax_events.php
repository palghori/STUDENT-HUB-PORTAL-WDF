<?php
// ajax_events.php - Single-Page AJAX Event Manager (Fetch API without Page Reload)
session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AJAX Event Manager - Practical 15</title>
  <link rel="stylesheet" href="../CSS/style.css" />
  <style>
    .loading-spinner {
      display: inline-block;
      width: 16px;
      height: 16px;
      border: 2px solid #ccc;
      border-top-color: #8b5e3c;
      border-radius: 50%;
      animation: spin 0.6s linear infinite;
      vertical-align: middle;
      margin-left: 8px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body>

  <?php include 'navbar.php'; ?>

  <main>
    <div class="dashboard-header">
      <div>
        <h1>⚡ AJAX / Fetch API Event Manager</h1>
        <p>Perform List, Add, Update, and Delete operations asynchronously without page reload</p>
      </div>
      <div>
        <button id="refreshBtn" class="btn btn-outline">🔄 Reload Data</button>
      </div>
    </div>

    <!-- Alert Box -->
    <div id="statusAlert" style="display: none;" class="alert"></div>

    <!-- Add / Edit Event Form Card -->
    <div class="card">
      <h2 id="formHeading">➕ Add New Event (AJAX Form)</h2>
      <form id="eventForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; align-items: flex-end;">
        <input type="hidden" id="editEventId" value="">

        <div class="form-group" style="margin-bottom: 0;">
          <label for="eventTitle">Event Title:</label>
          <input type="text" id="eventTitle" class="form-control" required placeholder="e.g. Cyber Security Workshop">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label for="eventCategory">Category:</label>
          <select id="eventCategory" class="form-control" required>
            <option value="Technical">Technical</option>
            <option value="Workshop">Workshop</option>
            <option value="Cultural">Cultural</option>
            <option value="Sports">Sports</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label for="eventDate">Event Date:</label>
          <input type="date" id="eventDate" class="form-control" required>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label for="eventDesc">Description:</label>
          <input type="text" id="eventDesc" class="form-control" placeholder="Short description...">
        </div>

        <div style="display: flex; gap: 8px;">
          <button type="submit" id="submitBtn" class="btn btn-primary" style="flex: 1;">Save Event</button>
          <button type="button" id="cancelEditBtn" class="btn btn-outline" style="display: none;">Cancel</button>
        </div>
      </form>
    </div>

    <!-- Asynchronous Events Table -->
    <div class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="margin: 0;">
          📋 Live Events List 
          <span id="loadingIndicator" style="display: none; font-size: 0.9rem; font-weight: normal; color: #888;">
            <span class="loading-spinner"></span> Loading from API...
          </span>
        </h2>
        <small style="color: #888;">Updated in real time via JSON API</small>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Category</th>
              <th>Date</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="eventsTableBody">
            <tr>
              <td colspan="6" style="text-align: center; padding: 25px; color: #888;">Loading events...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <footer>
    <p>StudentHub Portal &copy; 2026 | Practical 15 Final Integration</p>
  </footer>

  <!-- Fetch API Client Logic -->
  <script>
    const API_URL = 'api.php';

    const eventsTableBody = document.getElementById('eventsTableBody');
    const eventForm = document.getElementById('eventForm');
    const formHeading = document.getElementById('formHeading');
    const editEventId = document.getElementById('editEventId');
    const eventTitle = document.getElementById('eventTitle');
    const eventCategory = document.getElementById('eventCategory');
    const eventDate = document.getElementById('eventDate');
    const eventDesc = document.getElementById('eventDesc');
    const submitBtn = document.getElementById('submitBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const statusAlert = document.getElementById('statusAlert');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const refreshBtn = document.getElementById('refreshBtn');

    function showAlert(msg, isSuccess = true) {
      statusAlert.textContent = (isSuccess ? '✅ ' : '❌ ') + msg;
      statusAlert.className = isSuccess ? 'alert alert-success' : 'alert alert-error';
      statusAlert.style.display = 'block';
      setTimeout(() => { statusAlert.style.display = 'none'; }, 4000);
    }

    // 1. Fetch Events (GET)
    async function loadEvents() {
      loadingIndicator.style.display = 'inline-block';
      try {
        const res = await fetch(API_URL);
        const json = await res.json();
        loadingIndicator.style.display = 'none';

        if (json.success && json.data.length > 0) {
          eventsTableBody.innerHTML = '';
          json.data.forEach(evt => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td><strong>#${evt.event_id}</strong></td>
              <td><strong>${escapeHtml(evt.title)}</strong></td>
              <td><span class="badge badge-action">${escapeHtml(evt.category)}</span></td>
              <td>${escapeHtml(evt.event_date)}</td>
              <td>${escapeHtml(evt.description || '')}</td>
              <td>
                <button class="btn btn-outline" style="padding: 4px 8px; font-size: 0.8rem;" onclick="startEdit(${evt.event_id}, '${escapeAttr(evt.title)}', '${escapeAttr(evt.category)}', '${escapeAttr(evt.event_date)}', '${escapeAttr(evt.description || '')}')">✏️ Edit</button>
                <button class="btn btn-danger" style="padding: 4px 8px; font-size: 0.8rem;" onclick="deleteEvent(${evt.event_id})">🗑️ Delete</button>
              </td>
            `;
            eventsTableBody.appendChild(tr);
          });
        } else {
          eventsTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #888; padding: 25px;">No events found in database.</td></tr>';
        }
      } catch (err) {
        loadingIndicator.style.display = 'none';
        eventsTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: red; padding: 25px;">Failed to connect to API endpoint.</td></tr>';
      }
    }

    // 2. Handle Add / Edit Submission (POST / PUT)
    eventForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const id = editEventId.value;
      const isEditing = id !== '';

      const payload = {
        title: eventTitle.value.trim(),
        category: eventCategory.value,
        event_date: eventDate.value,
        description: eventDesc.value.trim()
      };

      if (isEditing) {
        payload.event_id = parseInt(id);
      }

      submitBtn.disabled = true;
      submitBtn.textContent = 'Saving...';

      try {
        const res = await fetch(API_URL, {
          method: isEditing ? 'PUT' : 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const result = await res.json();
        submitBtn.disabled = false;
        submitBtn.textContent = 'Save Event';

        if (result.success) {
          showAlert(result.message, true);
          resetForm();
          loadEvents();
        } else {
          showAlert(result.message || 'Operation failed', false);
        }
      } catch (err) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Save Event';
        showAlert('Network or server error', false);
      }
    });

    // 3. Delete Event (DELETE)
    window.deleteEvent = async function(id) {
      if (!confirm('Are you sure you want to delete this event via AJAX?')) return;

      try {
        const res = await fetch(API_URL, {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ event_id: id })
        });
        const result = await res.json();
        if (result.success) {
          showAlert(result.message, true);
          loadEvents();
        } else {
          showAlert(result.message || 'Delete failed', false);
        }
      } catch (err) {
        showAlert('Error during deletion', false);
      }
    };

    // Form Edit Helpers
    window.startEdit = function(id, title, cat, date, desc) {
      editEventId.value = id;
      eventTitle.value = title;
      eventCategory.value = cat;
      eventDate.value = date;
      eventDesc.value = desc;
      formHeading.textContent = `✏️ Edit Event #${id} (AJAX)`;
      submitBtn.textContent = 'Update Event';
      cancelEditBtn.style.display = 'inline-block';
      eventTitle.focus();
    };

    cancelEditBtn.addEventListener('click', resetForm);
    refreshBtn.addEventListener('click', loadEvents);

    function resetForm() {
      editEventId.value = '';
      eventForm.reset();
      formHeading.textContent = '➕ Add New Event (AJAX Form)';
      submitBtn.textContent = 'Save Event';
      cancelEditBtn.style.display = 'none';
    }

    function escapeHtml(str) {
      const div = document.createElement('div');
      div.innerText = str;
      return div.innerHTML;
    }

    function escapeAttr(str) {
      return (str || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    // Load initial events on page ready
    document.addEventListener('DOMContentLoaded', loadEvents);
  </script>
</body>
</html>
