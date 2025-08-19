//script para activar el modal de registro o edición de usuario

document.addEventListener('DOMContentLoaded', function () {
    const usuarioModal = document.getElementById('usuarioModal');
    
    usuarioModal.addEventListener('show.bs.modal', function (event) {
        // Botón que disparó el modal
        const button = event.relatedTarget;

        // Extraer información de los atributos data-*
        const id = button.getAttribute('data-id');
        
        // Elementos del formulario
        const modalTitle = usuarioModal.querySelector('.modal-title');
        const form = document.getElementById('usuarioForm');
        const formAccion = document.getElementById('formAccion');
        const formIdUsuario = document.getElementById('formIdUsuario');
        const inputNombre = document.getElementById('nom_usu');
        const inputPassword = document.getElementById('cla_usu');
        const labelPassword = document.getElementById('labelPassword');
        const selectRol = document.getElementById('fky_rol');
        const selectEstado = document.getElementById('est_usu');
        const btnSubmitText = document.querySelector('#btnSubmit span');
        const btnSubmitIcon = document.querySelector('#btnSubmit i');

        if (id) {
            // --- MODO EDICIÓN ---
            modalTitle.textContent = 'Editar Usuario';
            formAccion.value = 'modificar';
            formIdUsuario.value = id;

            // Llenar el formulario con los datos del botón
            inputNombre.value = button.getAttribute('data-nombre');
            selectRol.value = button.getAttribute('data-rol');
            selectEstado.value = button.getAttribute('data-estado');

            // La contraseña es opcional al editar
            labelPassword.textContent = 'Nueva Contraseña (opcional)';
            inputPassword.removeAttribute('required');

            // Actualizar botón de envío
            btnSubmitText.textContent = 'Guardar Cambios';
            btnSubmitIcon.className = 'fas fa-save';

        } else {
            // --- MODO REGISTRO ---
            modalTitle.textContent = 'Registrar Nuevo Usuario';
            form.reset(); // Limpiar el formulario
            formAccion.value = 'registrar';
            formIdUsuario.value = '';

            // La contraseña es obligatoria al registrar
            labelPassword.textContent = 'Contraseña *';
            inputPassword.setAttribute('required', 'required');
            
            // Actualizar botón de envío
            btnSubmitText.textContent = 'Registrar Usuario';
            btnSubmitIcon.className = 'fas fa-plus';
        }
    });
});