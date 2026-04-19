</main>

<footer class="ih-footer">
    <div class="container">
        <div class="row g-4 mb-3">
            <div class="col-md-4">
                <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="L'Assoce Info"
                     height="80" style="border-radius:.5rem" class="mb-3">
                <p class="small mb-0 opacity-75">
                    Site de l'association Assoce Info —<br>
                    Centre pédagogique du Nord vaudois<br>
                    Sainte-Croix, VD
                </p>
            </div>
            <div class="col-md-4">
                <div class="small fw-semibold text-white mb-2">Navigation</div>
                <ul class="list-unstyled small mb-0">
                    <li><a href="<?= BASE_URL ?>/concours.php"><i class="bi bi-trophy me-1"></i>Concours du mois</a></li>
                    <li><a href="<?= BASE_URL ?>/news.php"><i class="bi bi-newspaper me-1"></i>News</a></li>
                    <li><a href="<?= BASE_URL ?>/annonces.php"><i class="bi bi-tag me-1"></i>Annonces</a></li>
                    <li><a href="<?= BASE_URL ?>/pubs.php"><i class="bi bi-megaphone me-1"></i>Pubs</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <div class="small fw-semibold text-white mb-2">Contact</div>
                <ul class="list-unstyled small mb-0">
                    <li><a href="mailto:CPNV_Infohub@eduvaud.ch"><i class="bi bi-envelope me-1"></i>CPNV_Infohub@eduvaud.ch</a></li>
                    <?php if (!isLoggedIn()): ?>
                        <li class="mt-2"><a href="<?= BASE_URL ?>/login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Connexion membre</a></li>
                    <?php endif; ?>
                    <?php if (isAdmin()): ?>
                        <li class="mt-2"><a href="<?= BASE_URL ?>/admin/index.php"><i class="bi bi-speedometer2 me-1"></i>Administration</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <hr>
        <div class="d-flex justify-content-between align-items-center small">
            <span>&copy; <?= date('Y') ?> Assoce Info — CPNV Sainte-Croix</span>
            <a href="<?= BASE_URL ?>/admin/login.php" class="opacity-50"><i class="bi bi-lock"></i></a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
