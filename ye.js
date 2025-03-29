function editRow(button) {
    const row = button.closest('tr');
    const cells = row.querySelectorAll('td');

    for (let i = 1; i < cells.length - 1; i++) {
        const cell = cells[i];
        const currentValue = cell.innerText;
        cell.innerHTML = `<input type="text" value="${currentValue}">`;
    }

    const actionCell = cells[cells.length - 1];
    actionCell.innerHTML = `
        <button class="save-btn" onclick="saveRow(this)">Save</button>
        <button class="delete-btn" onclick="deleteRow(this)">Delete</button>
    `;
}

function saveRow(button) {
    const row = button.closest('tr');
    const cells = row.querySelectorAll('td');

    for (let i = 1; i < cells.length - 1; i++) {
        const cell = cells[i];
        const input = cell.querySelector('input');
        cell.innerText = input.value;
    }

    const actionCell = cells[cells.length - 1];
    actionCell.innerHTML = `
        <button class="edit-btn" onclick="editRow(this)">Edit</button>
        <button class="delete-btn" onclick="deleteRow(this)">Delete</button>
    `;
}

function deleteRow(button) {
    const row = button.closest('tr');
    row.remove();
}
