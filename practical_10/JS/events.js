let allEvents = [];
let filteredEvents = [];
let currentPage = 1;
const itemsPerPage = 5;

// Fetch events from JSON on page load
document.addEventListener("DOMContentLoaded", () => {
  fetch("../DATA/events.json")
    .then(response => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then(data => {
      allEvents = data;
      filteredEvents = [...allEvents];
      // Initial sort
      handleSort();
    })
    .catch(error => {
      console.error("Error fetching events:", error);
      document.getElementById("eventsList").innerHTML = "<p style='color:red;'>Failed to load events. Please try again later.</p>";
    });
});

function renderEvents() {
  const eventsList = document.getElementById("eventsList");
  eventsList.innerHTML = "";

  if (filteredEvents.length === 0) {
    eventsList.innerHTML = "<p>No events found matching your criteria.</p>";
    updatePagination();
    return;
  }

  // Calculate pagination
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const currentEvents = filteredEvents.slice(startIndex, endIndex);

  currentEvents.forEach(event => {
    const card = document.createElement("div");
    card.className = "event-card";
    
    // Formatting date
    const dateObj = new Date(event.date);
    const dateString = dateObj.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });

    card.innerHTML = `
      <h3 class="event-title">${event.title}</h3>
      <div class="event-meta">
        <span>Category: ${event.category}</span>
        <span>Date: ${dateString}</span>
      </div>
      <p>${event.description}</p>
    `;
    eventsList.appendChild(card);
  });

  updatePagination();
}

function updatePagination() {
  const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
  const pageInfo = document.getElementById("pageInfo");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");

  if (totalPages === 0) {
    pageInfo.innerText = "Page 0 of 0";
    prevBtn.disabled = true;
    nextBtn.disabled = true;
    return;
  }

  pageInfo.innerText = `Page ${currentPage} of ${totalPages}`;
  
  prevBtn.disabled = currentPage === 1;
  nextBtn.disabled = currentPage === totalPages;
}

function prevPage() {
  if (currentPage > 1) {
    currentPage--;
    renderEvents();
  }
}

function nextPage() {
  const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
  if (currentPage < totalPages) {
    currentPage++;
    renderEvents();
  }
}

function applyFilters() {
  const searchTerm = document.getElementById("searchInput").value.toLowerCase();
  const categoryFilter = document.getElementById("filterCategory").value;

  filteredEvents = allEvents.filter(event => {
    // Check search term
    const matchesSearch = event.title.toLowerCase().includes(searchTerm) || 
                          event.description.toLowerCase().includes(searchTerm);
    
    // Check category
    const matchesCategory = categoryFilter === "All" || event.category === categoryFilter;

    return matchesSearch && matchesCategory;
  });

  // Reset to first page when filtering
  currentPage = 1;
  
  // Re-apply sorting on the new filtered array
  applySort();
}

function handleSearch() {
  applyFilters();
}

function handleFilter() {
  applyFilters();
}

function handleSort() {
  applySort();
}

function applySort() {
  const sortOrder = document.getElementById("sortDate").value;

  filteredEvents.sort((a, b) => {
    const dateA = new Date(a.date).getTime();
    const dateB = new Date(b.date).getTime();

    if (sortOrder === "asc") {
      return dateA - dateB;
    } else {
      return dateB - dateA;
    }
  });

  renderEvents();
}
