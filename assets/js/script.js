
document.addEventListener('DOMContentLoaded', function () {

    const formularios = document.querySelectorAll(
        '.confirmar-exclusao'
    );

    formularios.forEach(function (formulario) {

        formulario.addEventListener('submit', function (evento) {

            const confirmar = window.confirm(
                'Tem certeza que deseja excluir este produto?'
            );

            if (!confirmar) {
                evento.preventDefault();
            }

        });

    });

});
