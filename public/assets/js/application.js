const Ajax = {
  async get(url) {
    try {
      const response = await fetch(url, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        return await response.json();
      }

      return { success: true };
    } catch (error) {
      console.error('AJAX Error:', error);
      throw error;
    }
  }
};

const Toast = {
  show(message, type = 'success') {
    const container = this.getContainer();
    const toast = document.createElement('div');

    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const icon = type === 'success' ? '✓' : '✕';

    toast.className = 'transform transition-all duration-300 translate-x-full';
    toast.innerHTML = `
      <div class="${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-[300px]">
        <span class="text-2xl">${icon}</span>
        <span class="flex-1">${message}</span>
        <button class="toast-close ml-2 text-white hover:text-gray-200 font-bold">×</button>
      </div>
    `;

    container.appendChild(toast);

    setTimeout(() => toast.classList.remove('translate-x-full'), 10);

    toast.querySelector('.toast-close').addEventListener('click', () => {
      toast.classList.add('translate-x-full');
      setTimeout(() => toast.remove(), 300);
    });

    setTimeout(() => {
      toast.classList.add('translate-x-full');
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  },

  getContainer() {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'fixed top-4 right-4 z-50 space-y-2';
      document.body.appendChild(container);
    }
    return container;
  }
};

async function searchEvents(searchTerm) {
  const resultsContainer = document.getElementById('events-results');
  const loadingIndicator = document.getElementById('events-loading');

  if (!resultsContainer) {
    return;
  }

  if (loadingIndicator) {
    loadingIndicator.classList.remove('hidden');
  }

  try {
    const response = await Ajax.get(`/events/search?q=${encodeURIComponent(searchTerm)}`);

    if (response.success) {
      renderEvents(response.events);
    } else {
      Toast.show('Erro ao buscar eventos', 'error');
    }
  } catch (error) {
    Toast.show('Erro ao buscar eventos', 'error');
    console.error(error);
  } finally {
    if (loadingIndicator) {
      loadingIndicator.classList.add('hidden');
    }
  }
}

function renderEvents(events) {
  const resultsContainer = document.getElementById('events-results');

  if (!resultsContainer) {
    return;
  }

  if (events.length === 0) {
    resultsContainer.innerHTML = `
      <tr>
        <td colspan="8" class="px-6 py-4 text-center text-white">
          Nenhum evento encontrado
        </td>
      </tr>
    `;
    return;
  }

  resultsContainer.innerHTML = events.map(event => {
    const startDate = event.start_date ? new Date(event.start_date).toLocaleDateString('pt-BR') : '-';
    return `
    <tr class="hover:bg-[#14254E] transition-colors">
      <td class="py-4 px-6">
        <a href="/events/${event.id}" class="text-[#4A90E2] hover:text-[#2B6CE4] hover:underline">
          #${event.id}
        </a>
      </td>
      <td class="py-4 px-6 text-white font-medium">${event.name}</td>
      <td class="py-4 px-6 text-white">${event.event_type || '-'}</td>
      <td class="py-4 px-6 text-white">${startDate}</td>
      <td class="py-4 px-6 text-white">${event.workload_hours ? event.workload_hours + 'h' : '-'}</td>
      <td class="py-4 px-6 text-white">-</td>
      <td class="py-4 px-6">
        <span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-medium">Ativo</span>
      </td>
      <td class="py-4 px-6">
        <div class="flex justify-end gap-2">
          <a href="/events/${event.id}" class="text-[#4A90E2] hover:text-[#2B6CE4] p-2 rounded transition-colors" title="Visualizar">
            <i class="bi bi-eye text-lg"></i>
          </a>
          <a href="/events/${event.id}/participants" class="text-green-500 hover:text-green-400 p-2 rounded transition-colors" title="Gerenciar Participantes">
            <i class="bi bi-people text-lg"></i>
          </a>
          <a href="/events/${event.id}/edit" class="text-[#4A90E2] hover:text-[#2B6CE4] p-2 rounded transition-colors" title="Editar">
            <i class="bi bi-pencil-square text-lg"></i>
          </a>
        </div>
      </td>
    </tr>
  `;
  }).join('');
}

document.addEventListener("DOMContentLoaded", function () {

  const imagePreviewInput = document.getElementById("image_preview_input");
  const preview = document.getElementById("image_preview");
  const imagePreviewSubmit = document.getElementById("image_preview_submit");

  if (imagePreviewInput && preview) {
    imagePreviewInput.style.display = "none";
    imagePreviewSubmit.style.display = "none";

    preview.addEventListener("click", function () {
      imagePreviewInput.click();
    });

    imagePreviewInput.addEventListener("change", function (event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById("image_preview").src = e.target.result;
          imagePreviewSubmit.style.display = "block";
        };
        reader.readAsDataURL(file);
      }
    });
  }

  const searchInput = document.getElementById('event-search-input');
  if (searchInput) {
    let searchTimeout;

    searchInput.addEventListener('input', (e) => {
      clearTimeout(searchTimeout);
      const searchTerm = e.target.value.trim();

      searchTimeout = setTimeout(() => {
        searchEvents(searchTerm);
      }, 300);
    });
  }

});
