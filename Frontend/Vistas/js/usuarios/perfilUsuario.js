document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('perfilClaveForm');
    if (!form) return;

    const claveNueva = document.getElementById('cla_nueva');
    const claveConfirmar = document.getElementById('cla_confirmar');
    const feedbackConfirmacion = document.getElementById('feedbackConfirmacion');

    const validarCoincidencia = () => {
        if (claveConfirmar.value !== '' && claveNueva.value !== claveConfirmar.value) {
            claveConfirmar.setCustomValidity('Las contrasenas no coinciden');
            feedbackConfirmacion.textContent = 'La confirmacion debe coincidir exactamente con la nueva contrasena.';
            return;
        }

        claveConfirmar.setCustomValidity('');
        feedbackConfirmacion.textContent = 'Debes confirmar la nueva contrasena.';
    };

    claveNueva.addEventListener('input', validarCoincidencia);
    claveConfirmar.addEventListener('input', validarCoincidencia);

    form.addEventListener('submit', (event) => {
        validarCoincidencia();

        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    });
});
