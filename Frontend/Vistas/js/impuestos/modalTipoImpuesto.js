document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalTipoImpuesto');
    if (!modal) return;

    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget || {};
        const form = document.getElementById('tipoImpuestoForm');
        const accion = document.getElementById('formAccionTipo');
        const idInput = document.getElementById('formIdTipo');
        const nombreInput = document.getElementById('nom_tip');
        const descripcionInput = document.getElementById('des_tip');
        const title = modal.querySelector('.modal-title');
        const submitBtn = document.getElementById('btnSubmitTipo');
        const btnIcon = submitBtn.querySelector('i');
        const btnText = submitBtn.querySelector('span');

        if (button.dataset && button.dataset.id) {
            title.textContent = 'Editar Tipo de Impuesto';
            accion.value = 'modificar';
            idInput.value = button.dataset.id;
            nombreInput.value = button.dataset.nombre || '';
            descripcionInput.value = button.dataset.descripcion || '';
            btnIcon.className = 'fas fa-save';
            btnText.textContent = 'Guardar Cambios';
        } else {
            form.reset();
            title.textContent = 'Registrar Tipo de Impuesto';
            accion.value = 'registrar';
            idInput.value = '';
            btnIcon.className = 'fas fa-plus';
            btnText.textContent = 'Registrar';
        }
    });
});
