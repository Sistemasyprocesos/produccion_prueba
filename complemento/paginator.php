<script>
document.addEventListener("DOMContentLoaded", function () {

  const rowsPerPage = 10;
  const table = document.getElementById("tblcolab");
  const tbody = table.querySelector("tbody");
  const allRows = Array.from(tbody.querySelectorAll("tr"));
  const pagination = document.getElementById("pagination");
  const buscador = document.getElementById("Buscador");

  let currentPage = 1;
  let filteredRows = [...allRows];

  function displayRows() {
    tbody.innerHTML = "";
    let start = (currentPage - 1) * rowsPerPage;
    let end = start + rowsPerPage;
    filteredRows.slice(start, end).forEach(row => tbody.appendChild(row));
  }

  function createPagination() {
    pagination.innerHTML = "";
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
    if (totalPages <= 1) return;

    // Anterior
    pagination.innerHTML += `
      <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="prev">Anterior</a>
      </li>`;

    // Números
    for (let i = 1; i <= totalPages; i++) {
      pagination.innerHTML += `
        <li class="page-item ${i === currentPage ? 'active' : ''}">
          <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>`;
    }

    // Siguiente
    pagination.innerHTML += `
      <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="next">Siguiente</a>
      </li>`;
  }

  pagination.addEventListener("click", function (e) {
    e.preventDefault();
    const page = e.target.dataset.page;
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);

    if (page === "prev" && currentPage > 1) currentPage--;
    else if (page === "next" && currentPage < totalPages) currentPage++;
    else if (!isNaN(page)) currentPage = parseInt(page);

    update();
  });

  buscador.addEventListener("keyup", function () {
    const filtro = this.value.toLowerCase();
    filteredRows = allRows.filter(row =>
      row.textContent.toLowerCase().includes(filtro)
    );
    currentPage = 1;
    update();
  });

  function update() {
    displayRows();
    createPagination();
  }

  update();
});
</script>
