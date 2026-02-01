import './bootstrap';
import { Toast } from 'bootstrap';

const APP_URL = document.querySelector('meta[name="app-url"]')?.getAttribute('content') + '/' || '/';

window.showToast = function(message, type = 'success') {
    let toastElement = document.getElementById("toastMessage");
    toastElement.classList.remove("bg-success", "bg-danger", "bg-warning", "bg-info");
    toastElement.classList.add("bg-" + type);

    let toastBody = toastElement.querySelector(".toast-body");
    toastBody.textContent = message;

    let toast = new Toast(toastElement);
    toast.show();
};

$(document).ready(function () {
    $(".btn-modal").on("click", function () {
        let selectId = this.dataset.select;
        let routing = this.dataset.routing == 'TipoMateria' ? APP_URL + 'tipo-materia-prima/inserir' : APP_URL + 'tipo-contato/inserir';
        let descricao = document.querySelector("input[name='descricao']").value;
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

        if (!descricao) {
            showToast('Informe a descrição.', 'danger');
            return;
        }

        axios.post(routing, { descricao, csrfToken })
            .then(response => {
                $('.btn-close').click();

                let novoId = response.data.id;
                let selectElement = document.querySelector(selectId);

                let novaOpcao = new Option(descricao, novoId, true, true);
                selectElement.appendChild(novaOpcao);

                showToast('Cadastro realizado com sucesso.', 'success');
            })
            .catch(error => {
                showToast(error.response?.data?.message || 'Erro ao cadastrar', 'danger');
            });

    });
});