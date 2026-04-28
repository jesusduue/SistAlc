document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (event) => {
        const botonEstado = event.target.closest('.btn-cambiar-estado-rol');
        if (!botonEstado || botonEstado.classList.contains('disabled')) {
            return;
        }

        event.preventDefault();

        const url = botonEstado.getAttribute('data-url');
        const nombre = botonEstado.getAttribute('data-nombre') || 'rol';
        const accion = botonEstado.getAttribute('data-accion') || 'actualizar';

        Swal.fire({
            title: 'Confirmar accion',
            text: `Se va a ${accion} el rol "${nombre}".`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7f1d1d',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Si, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
