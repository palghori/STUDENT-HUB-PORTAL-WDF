// app.js — Fetch API calls to consume the PHP JSON API
// All operations happen WITHOUT page reload

const API_URL = "api.php";

// ─────────────────────────────────────────────
// UTILITY: Show a status message
// ─────────────────────────────────────────────
function showMessage(text, isSuccess) {
    const box = document.getElementById("status-msg");
    box.textContent = text;
    box.style.color = isSuccess ? "#4CAF50" : "#f44336";
    box.style.display = "block";

    // Auto-hide after 3 seconds
    setTimeout(() => { box.style.display = "none"; }, 3000);
}

// ─────────────────────────────────────────────
// UTILITY: Show / hide loading spinner
// ─────────────────────────────────────────────
function setLoading(visible) {
    document.getElementById("loading").style.display = visible ? "block" : "none";
}

// ─────────────────────────────────────────────
// GET — Load all events and render in table
// ─────────────────────────────────────────────
async function loadEvents() {
    setLoading(true);
    try {
        const response = await fetch(API_URL);        // GET request
        const result   = await response.json();

        if (result.success) {
            renderTable(result.data);
        } else {
            showMessage("Error loading events: " + result.message, false);
        }
    } catch (err) {
        showMessage("Network error: " + err.message, false);
    }
    setLoading(false);
}

// ─────────────────────────────────────────────
// Render events array into the HTML table
// ─────────────────────────────────────────────
function renderTable(events) {
    const tbody = document.getElementById("events-tbody");
    tbody.innerHTML = ""; // clear existing rows

    if (events.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#aaa;">No events found. Add one above!</td></tr>';
        return;
    }

    events.forEach((ev, index) => {
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${ev.title}</td>
                <td>${ev.category}</td>
                <td>${ev.event_date}</td>
                <td>
                    <button class="btn-edit"   onclick="openEditForm(${ev.event_id}, '${ev.title}', '${ev.category}', '${ev.event_date}', \`${ev.description}\`)">Edit</button>
                    <button class="btn-delete" onclick="deleteEvent(${ev.event_id})">Delete</button>
                </td>
            </tr>`;
        tbody.insertAdjacentHTML("beforeend", row);
    });
}

// ─────────────────────────────────────────────
// POST — Add a new event
// ─────────────────────────────────────────────
async function addEvent(event) {
    event.preventDefault(); // Stop page from reloading

    const title      = document.getElementById("add-title").value.trim();
    const category   = document.getElementById("add-category").value;
    const event_date = document.getElementById("add-date").value;
    const description= document.getElementById("add-desc").value.trim();

    // Frontend validation
    if (!title || !event_date) {
        showMessage("Title and Date are required.", false);
        return;
    }

    setLoading(true);
    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ title, category, event_date, description })
        });
        const result = await response.json();
        showMessage(result.message, result.success);

        if (result.success) {
            document.getElementById("add-form").reset(); // Clear the form
            loadEvents();                                 // Refresh the table
        }
    } catch (err) {
        showMessage("Network error: " + err.message, false);
    }
    setLoading(false);
}

// ─────────────────────────────────────────────
// Open the edit form and fill it with data
// ─────────────────────────────────────────────
function openEditForm(id, title, category, date, description) {
    document.getElementById("edit-section").style.display = "block";
    document.getElementById("edit-id").value          = id;
    document.getElementById("edit-title").value       = title;
    document.getElementById("edit-category").value    = category;
    document.getElementById("edit-date").value        = date;
    document.getElementById("edit-desc").value        = description;
    document.getElementById("edit-section").scrollIntoView({ behavior: "smooth" });
}

function closeEditForm() {
    document.getElementById("edit-section").style.display = "none";
}

// ─────────────────────────────────────────────
// PUT — Update an event
// ─────────────────────────────────────────────
async function updateEvent(event) {
    event.preventDefault();

    const event_id   = document.getElementById("edit-id").value;
    const title      = document.getElementById("edit-title").value.trim();
    const category   = document.getElementById("edit-category").value;
    const event_date = document.getElementById("edit-date").value;
    const description= document.getElementById("edit-desc").value.trim();

    if (!title || !event_date) {
        showMessage("Title and Date are required.", false);
        return;
    }

    setLoading(true);
    try {
        const response = await fetch(API_URL, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ event_id, title, category, event_date, description })
        });
        const result = await response.json();
        showMessage(result.message, result.success);

        if (result.success) {
            closeEditForm();
            loadEvents(); // Refresh table without reload
        }
    } catch (err) {
        showMessage("Network error: " + err.message, false);
    }
    setLoading(false);
}

// ─────────────────────────────────────────────
// DELETE — Delete an event
// ─────────────────────────────────────────────
async function deleteEvent(event_id) {
    if (!confirm("Are you sure you want to delete this event?")) return;

    setLoading(true);
    try {
        const response = await fetch(API_URL, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ event_id })
        });
        const result = await response.json();
        showMessage(result.message, result.success);

        if (result.success) {
            loadEvents(); // Refresh table without reload
        }
    } catch (err) {
        showMessage("Network error: " + err.message, false);
    }
    setLoading(false);
}

// ─────────────────────────────────────────────
// Load events when page first opens
// ─────────────────────────────────────────────
window.addEventListener("DOMContentLoaded", loadEvents);
