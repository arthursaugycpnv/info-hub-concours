</main>

<footer class="ih-footer">
    <div class="container">
        <div class="row g-4 mb-2">
            <div class="col-md-4">
                <div style="display:inline-flex;align-items:center;gap:.6rem;background:var(--nb-yellow);border:4px solid #fff;padding:.5rem 1rem;box-shadow:4px 4px 0 0 #fff;margin-bottom:.75rem">
                    <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="L'Assoce Info" height="36" style="border:2px solid #000">
                    <div>
                        <div style="font-weight:900;font-size:1rem;text-transform:uppercase;color:#000;line-height:1">ASSO<span style="color:var(--nb-pink)">.INFO</span></div>
                        <div style="font-family:'Courier New',monospace;font-size:.6rem;color:#333">CPNV Sainte-Croix</div>
                    </div>
                </div>
                <p class="nb-mono small mb-0" style="color:#777">
                    Section informatique<br>
                    Centre pédagogique du Nord vaudois<br>
                    Sainte-Croix, VD
                </p>
            </div>
            <div class="col-md-4">
                <div class="fw-bold text-white mb-2 nb-upper" style="font-size:.75rem;letter-spacing:.08em">Navigation</div>
                <ul class="list-unstyled nb-mono small mb-0" style="color:#777">
                    <li><a href="<?= BASE_URL ?>/concours.php">→ Concours du mois</a></li>
                    <li><a href="<?= BASE_URL ?>/news.php">→ News</a></li>
                    <li><a href="<?= BASE_URL ?>/annonces.php">→ Annonces</a></li>
                    <li><a href="<?= BASE_URL ?>/pubs.php">→ Pubs</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <div class="fw-bold text-white mb-2 nb-upper" style="font-size:.75rem;letter-spacing:.08em">Contact</div>
                <ul class="list-unstyled nb-mono small mb-0" style="color:#777">
                    <li><a href="mailto:CPNV_Infohub@eduvaud.ch">→ CPNV_Infohub@eduvaud.ch</a></li>
                    <?php if (!isLoggedIn()): ?>
                        <li class="mt-2"><a href="<?= BASE_URL ?>/login.php">→ Connexion membre</a></li>
                    <?php endif; ?>
                    <?php if (isAdmin()): ?>
                        <li class="mt-2"><a href="<?= BASE_URL ?>/admin/index.php">→ Administration</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <hr>
        <div class="d-flex justify-content-between align-items-center">
            <span class="nb-mono" style="font-size:.75rem;color:#555">© <?= date('Y') ?> Assoce Info — CPNV Sainte-Croix</span>
            <div class="d-flex gap-2 align-items-center">
                <div class="nb-footer-deco" style="background:var(--nb-yellow);transform:rotate(12deg)"></div>
                <div class="nb-footer-deco" style="background:var(--nb-cyan);transform:rotate(-6deg)"></div>
                <div class="nb-footer-deco" style="background:var(--nb-pink);transform:rotate(3deg)"></div>
                <a href="<?= BASE_URL ?>/admin/login.php" style="opacity:.3;color:#fff;margin-left:.5rem"><i class="bi bi-lock"></i></a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){var t='<?= csrf_token() ?>';document.querySelectorAll('form').forEach(function(f){if(f.method.toLowerCase()==='post'&&!f.querySelector('[name="csrf_token"]')){var i=document.createElement('input');i.type='hidden';i.name='csrf_token';i.value=t;f.appendChild(i);}});})();
</script>
</body>
</html>
