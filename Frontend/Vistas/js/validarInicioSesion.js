

/* const confirmarInicioSesion = () => {

    let xnom_usu = document.querySelector("[name=nom_usu]").value;
    let xcla_usu = document.querySelector("[name=cla_usu]").value;

    //expresion regular para validar el inicio de sesion
    const patronNombre = /^[a-zA-Z0-9_]{3,20}$/g;
    const patronClave = /^[a-zA-Z0-9@#$%^&*]{6,20}$/g;

    const validaNombre = patronNombre.test(xnom_usu);
    if (validaNombre) {
        document.querySelector("[name=nom_usu]").classList.add("is-valid");
        document.querySelector("[name=nom_usu]").nextElementSibling.innerHTML = "";
        document.querySelector("[name=nom_usu]").classList.remove("is-invalid");
    }else{
        document.querySelector("[name=nom_usu]").classList.remove("is-valid");
        document.querySelector("[name=nom_usu]").classList.add("is-invalid");
        document.querySelector("[name=nom_usu]").nextElementSibling.innerHTML = "El nombre de usuario no es válido.";
    }
    const validaClave = patronClave.test(xcla_usu);
    if (validaClave) {
        document.querySelector("[name=cla_usu]").classList.add("is-valid");
        document.querySelector("[name=cla_usu]").nextElementSibling.innerHTML = "";
        document.querySelector("[name=cla_usu]").classList.remove("is-invalid");
    }else{
        document.querySelector("[name=cla_usu]").classList.remove("is-valid");
        document.querySelector("[name=cla_usu]").classList.add("is-invalid");
        document.querySelector("[name=cla_usu]").nextElementSibling.innerHTML = "La contraseña no es válida.";
        /* alert(`clave valida!`) 
    }


}; 

// Selecciona el formulario y agrega el evento de submit

 //   const formu = document.getElementById("loginForm");
     const confirmarInicio = (evento) => {
        evento.preventDefault();
        confirmarInicioSesion();
    } 
     formu.addEventListener("submit", confirmarInicio);  

 // funcion para validar el inicio de sesion con el evento blur
    const validarCampo = (input) => {
        const eventoBlur = (evento) => {
            evento.preventDefault();
            confirmarInicioSesion();
        };
        input.addEventListener("blur", eventoBlur);
    };

    const inputs = formu.querySelectorAll("input");
    inputs.forEach(validarCampo); */

 // Validación + confirmación con SweetAlert

const confirmarInicioSesion = () => {
    let xnom_usu = document.querySelector("[name=nom_usu]").value;
    let xcla_usu = document.querySelector("[name=cla_usu]").value;

    const patronNombre = /^[a-zA-Z0-9_]{3,20}$/g;
    const patronClave = /^[a-zA-Z0-9@#$%^&*]{6,20}$/g;

    let valido = true;

    // Validación del nombre
    if (patronNombre.test(xnom_usu)) {
        document.querySelector("[name=nom_usu]").classList.add("is-valid");
        document.querySelector("[name=nom_usu]").classList.remove("is-invalid");
        document.querySelector("[name=nom_usu]").nextElementSibling.innerHTML = "";
    } else {
        document.querySelector("[name=nom_usu]").classList.remove("is-valid");
        document.querySelector("[name=nom_usu]").classList.add("is-invalid");
        document.querySelector("[name=nom_usu]").nextElementSibling.innerHTML = "El nombre de usuario no es válido.";
        valido = false;
    }

    // Validación de la clave
    if (patronClave.test(xcla_usu)) {
        document.querySelector("[name=cla_usu]").classList.add("is-valid");
        document.querySelector("[name=cla_usu]").classList.remove("is-invalid");
        document.querySelector("[name=cla_usu]").nextElementSibling.innerHTML = "";
    } else {
        document.querySelector("[name=cla_usu]").classList.remove("is-valid");
        document.querySelector("[name=cla_usu]").classList.add("is-invalid");
        document.querySelector("[name=cla_usu]").nextElementSibling.innerHTML = "La contraseña no es válida.";
        valido = false;
    }

    return valido;
};

// Confirmación previa antes de enviar
document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevenir el envío inmediato

    if (confirmarInicioSesion()) {
        // Mostrar alerta de carga
        Swal.fire({
            title: 'Iniciando sesión...',
            text: 'Por favor, espere',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
            timer: 5000,
            timerProgressBar: true
        });

        // Enviar el formulario después del tiempo
        setTimeout(() => {
            this.submit();
        }, 1000);
    } else {
        Swal.fire({
            title: 'Campos inválidos',
            text: 'Por favor, corrige los errores antes de continuar.',
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
    }
});

// Validación en tiempo real con blur
document.querySelectorAll("#loginForm input").forEach((input) => {
    input.addEventListener("blur", confirmarInicioSesion);
});
   