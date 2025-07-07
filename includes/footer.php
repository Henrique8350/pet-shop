<footer class="bg-primary text-white py-1 mt-5">
    <div class="container d-flex flex-column flex-md-row justify-content-md-between align-items-center small text-center text-md-start">

        <div>
            <span class="fw-bold me-2">© <?= date('Y') ?> - asafegnr</span>
            <a href="https://wa.me/5581996827136" target="_blank" class="text-white text-decoration-none me-2">
                <i class="fab fa-whatsapp me-1"></i>(81) 99682-7136
            </a>
            <a href="https://github.com/asafegnr" target="_blank" class="text-white text-decoration-none">
                <i class="fab fa-github me-1"></i>github.com/asafegnr
            </a>
        </div>

        <div class="mt-2 mt-md-0">
            <a href="#" class="text-white text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                Deixe seu Feedback
            </a>
        </div>

    </div>
    <hr class="border-light my-1">
    <div class="text-center small">
        <span>Desenvolvido com ❤️ por <strong>asafegnr e equipe</strong></span>
    </div>
</footer>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

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
        font-size: 0.8rem;
    }
</style>

<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="feedbackModalLabel">Seu Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="modalFeedback" class="form-label">Sua opinião é importante:</label>
                        <textarea id="modalFeedback" name="feedback" rows="4" class="form-control"
                            placeholder="Deixe seu feedback aqui..." required></textarea>
                        <div class="invalid-feedback">
                            Por favor, escreva seu feedback antes de enviar.
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Enviar Feedback</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>