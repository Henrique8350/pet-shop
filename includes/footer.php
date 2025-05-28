<footer class="bg-primary text-white pt-4 pb-3 mt-5">
    <div class="container">
        <div class="row align-items-start justify-content-between">

            <!-- Informações -->
            <div class="col-md-5 mb-4 mb-md-0">
                <h5 class="fw-bold mb-3">© <?= date('Y') ?> - asafegnr</h5>
                <p class="small mb-2">Todos os direitos reservados.</p>

                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <i class="fab fa-whatsapp me-2"></i>
                        <a href="https://wa.me/5581996827136" target="_blank" class="text-white text-decoration-none fw-semibold">
                            (81) 99682-7136
                        </a>
                    </li>
                    <li>
                        <i class="fab fa-github me-2"></i>
                        <a href="https://github.com/asafegnr" target="_blank" class="text-white text-decoration-none fw-semibold">
                            github.com/asafegnr
                        </a>
                    </li>
                </ul>

                <p class="small fw-semibold mt-3 mb-1">Colaboradores:</p>
                <ul class="list-inline small">
                    <li class="list-inline-item me-3">Asafe</li>
                    <li class="list-inline-item me-3">Paulo</li>
                    <li class="list-inline-item me-3">Mariah</li>
                    <li class="list-inline-item me-3">Edlaine</li>
                    <li class="list-inline-item me-3">Julia</li>
                    <li class="list-inline-item">Vitor</li>
                </ul>
            </div>

            <!-- Formulário de Feedback -->
            <div class="col-md-6">
                <form action="#" method="post" class="needs-validation" novalidate>
                    <label for="feedback" class="form-label small fw-semibold">Deixe seu feedback:</label>
                    <textarea id="feedback" name="feedback" rows="3" class="form-control rounded-3 shadow-sm border-0"
                        placeholder="Sua opinião é importante..." required></textarea>
                    <div class="invalid-feedback">
                        Por favor, escreva seu feedback antes de enviar.
                    </div>
                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-light fw-semibold">Enviar</button>
                    </div>
                </form>
            </div>

        </div>
        <hr class="border-light my-3">
        <div class="text-center small">
            <span>Desenvolvido com ❤️ por <strong>asafegnr e equipe</strong></span>
        </div>
    </div>
</footer>

<!-- Font Awesome (Ícones) -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<!-- Validação do formulário -->
<script>
(() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})();
</script>

<style>
    footer {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    footer a {
        color: #fff;
    }
    footer a:hover {
        color: #d1d5db;
        text-decoration: underline;
    }
    footer i {
        font-size: 1.1rem;
    }
    textarea:focus, input:focus {
        box-shadow: 0 0 0 0.15rem rgba(255, 255, 255, 0.4);
    }
</style>
