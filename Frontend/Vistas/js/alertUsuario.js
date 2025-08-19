// Asegúrate de tener SweetAlert2 cargado antes de este archivo

document.addEventListener("DOMContentLoaded", () => {
  // Usamos delegación: escuchamos un único click en el documento
  document.addEventListener("click", (e) => {
    // Buscar si el click fue en (o dentro de) un botón de cambiar estado
    const btnEstado = e.target.closest(".btn-cambiar-estado");
    if (btnEstado) {
      e.preventDefault();
      const url    = btnEstado.getAttribute("data-url");
      const nombre = btnEstado.getAttribute("data-nombre") || "usuario";

      Swal.fire({
        title: "¿Está seguro?",
        text: `Se cambiará el estado del usuario "${nombre}".`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, cambiar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) window.location.href = url;
      });

      return; // importante: salir para no evaluar también la rama de eliminar
    }

    // Buscar si el click fue en (o dentro de) un botón de eliminar
    const btnEliminar = e.target.closest(".btn-eliminar-usuario");
    if (btnEliminar) {
      e.preventDefault();
      const url    = btnEliminar.getAttribute("data-url");
      const nombre = btnEliminar.getAttribute("data-nombre") || "usuario";

      Swal.fire({
        title: "¿Está seguro?",
        text: `Se eliminará al usuario "${nombre}". Esta acción no se puede deshacer.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) window.location.href = url;
      });
    }
  });
});
