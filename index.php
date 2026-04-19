<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'InfoHub CPNV — Accueil';
$db = getDB();

$concours = $db->query('SELECT * FROM concours WHERE actif = 1 ORDER BY created_at DESC LIMIT 1')->fetch();
$news     = $db->query('SELECT * FROM news ORDER BY created_at DESC LIMIT 3')->fetchAll();
$annonces = $db->query('SELECT * FROM annonces WHERE actif = 1 ORDER BY created_at DESC LIMIT 6')->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['registered'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4">
        <i class="bi bi-check-circle me-2"></i>
        Bienvenue ! Votre compte a été créé. Vous pouvez maintenant commenter les annonces et poster des publicités.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- HERO -->
<div class="nb-hero mb-5">
    <div class="nb-hero-deco-1"></div>
    <div class="nb-hero-deco-2"></div>
    <div class="row align-items-center g-4 position-relative">
        <div class="col-lg-7">
            <div class="nb-badge-hero"><i class="bi bi-geo-alt me-1"></i>Section informatique — Sainte-Croix, VD</div>
            <h1>Bienvenue sur<br><span style="color:var(--nb-yellow)">InfoHub</span></h1>
            <p class="lead mt-3 mb-4">
                Retrouvez le concours du mois, les dernières news de l'école
                et les annonces de la communauté.
            </p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= BASE_URL ?>/concours.php" class="btn btn-warning">
                    <i class="bi bi-trophy me-1"></i>Voir le concours
                </a>
                <a href="<?= BASE_URL ?>/annonces.php" class="btn btn-outline-light">
                    <i class="bi bi-tag me-1"></i>Annonces
                </a>
                <?php if (!isLoggedIn()): ?>
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-outline-light">
                        <i class="bi bi-person-plus me-1"></i>Rejoindre
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-5 d-none d-lg-flex justify-content-end gap-3">
            <div class="nb-stat" style="background:var(--nb-yellow)">
                <div class="nb-stat-value"><?= $db->query('SELECT COUNT(*) FROM news')->fetchColumn() ?></div>
                <div class="nb-mono" style="font-size:.7rem;margin-top:.25rem">News</div>
            </div>
            <div class="nb-stat" style="background:var(--nb-cyan)">
                <div class="nb-stat-value"><?= $db->query('SELECT COUNT(*) FROM annonces WHERE actif=1')->fetchColumn() ?></div>
                <div class="nb-mono" style="font-size:.7rem;margin-top:.25rem">Annonces</div>
            </div>
            <div class="nb-stat" style="background:var(--nb-pink);color:#fff">
                <div class="nb-stat-value"><?= $db->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn() ?></div>
                <div class="nb-mono" style="font-size:.7rem;margin-top:.25rem">Membres</div>
            </div>
        </div>
    </div>
</div>

<?php if ($concours): ?>
<!-- CONCOURS DU MOIS -->
<section class="mb-5">
    <div class="nb-section-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="nb-tag nb-tag-pink d-inline-block mb-2"><i class="bi bi-trophy-fill me-1"></i>Concours du mois</div>
                <h2 class="nb-section-title"><?= htmlspecialchars($concours['titre']) ?></h2>
                <?php if ($concours['deadline']): ?>
                    <?php
                        $deadline = new DateTime($concours['deadline']);
                        $today    = new DateTime();
                        $jours    = (int)$today->diff($deadline)->format('%r%a');
                    ?>
                    <p class="nb-mono small mt-1 mb-0">
                        Deadline : <strong><?= $deadline->format('d.m.Y') ?></strong>
                        <?php if ($jours > 0): ?>
                            — <?= $jours ?> jour<?= $jours > 1 ? 's' : '' ?> restant<?= $jours > 1 ? 's' : '' ?>
                        <?php elseif ($jours === 0): ?>
                            — <span style="color:var(--nb-pink);font-weight:900">C'est aujourd'hui !</span>
                        <?php else: ?>
                            — <span style="color:var(--nb-pink)">Délai dépassé</span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
            <?php if ($concours['prix']): ?>
                <div style="background:var(--nb-yellow);border:var(--nb-border);padding:.75rem 1.25rem;box-shadow:var(--nb-shadow-sm);text-align:center">
                    <div style="font-size:1.8rem;font-weight:900;line-height:1">CHF <?= number_format($concours['prix'], 0, '.', "'") ?></div>
                    <div class="nb-mono" style="font-size:.65rem">Prix à gagner</div>
                </div>
            <?php endif; ?>
        </div>
        <p class="mt-3 mb-3" style="white-space:pre-line"><?= htmlspecialchars(mb_substr($concours['description'], 0, 300)) ?>…</p>
        <a href="<?= BASE_URL ?>/concours.php" class="btn btn-dark">
            <i class="bi bi-arrow-right me-1"></i>Voir le concours complet
        </a>
    </div>
</section>
<?php endif; ?>

<!-- NEWS -->
<section class="mb-5">
    <div class="nb-section-header mb-4">
        <div class="nb-tag nb-tag-cyan d-inline-block"><i class="bi bi-newspaper me-1"></i>Actualités</div>
        <h2 class="nb-section-title">Dernières news</h2>
    </div>
    <?php if ($news): ?>
        <div class="row g-4">
            <?php $colors = ['var(--nb-yellow)','var(--nb-cyan)','var(--nb-pink)']; $ci = 0; ?>
            <?php foreach ($news as $article): ?>
                <div class="col-md-4">
                    <a href="<?= BASE_URL ?>/news.php?id=<?= $article['id'] ?>" class="text-decoration-none text-reset">
                        <div class="card h-100">
                            <?php if ($article['image']): ?>
                                <img src="<?= BASE_URL . htmlspecialchars($article['image']) ?>"
                                     class="card-img-top" alt="" style="height:160px;object-fit:cover">
                            <?php else: ?>
                                <div style="height:8px;background:<?= $colors[$ci % 3] ?>;border-bottom:var(--nb-border)"></div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <div class="nb-tag d-inline-block mb-2" style="background:<?= $colors[$ci % 3] ?>;transform:none"><?= date('d.m.Y', strtotime($article['created_at'])) ?></div>
                                <h3 class="h6 fw-bold mb-2 nb-upper"><?= htmlspecialchars($article['titre']) ?></h3>
                                <p class="small flex-grow-1" style="color:#555">
                                    <?= htmlspecialchars(mb_substr($article['contenu'], 0, 110)) ?>…
                                </p>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <span class="nb-mono" style="font-size:.7rem;color:#555"><i class="bi bi-person me-1"></i><?= htmlspecialchars($article['auteur']) ?></span>
                                <span class="btn btn-dark btn-sm">Lire <i class="bi bi-arrow-right ms-1"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php $ci++; ?>
            <?php endforeach; ?>
        </div>
        <div class="text-end mt-3">
            <a href="<?= BASE_URL ?>/news.php" class="btn btn-outline-dark btn-sm">
                Toutes les news <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    <?php else: ?>
        <p class="nb-mono" style="color:#555">Aucune news pour le moment.</p>
    <?php endif; ?>
</section>

<!-- ANNONCES -->
<section class="mb-5">
    <div class="nb-section-header mb-4">
        <div class="nb-tag nb-tag-yellow d-inline-block"><i class="bi bi-tag-fill me-1"></i>Annonces récentes</div>
        <h2 class="nb-section-title">Petites annonces</h2>
    </div>
    <?php if ($annonces): ?>
        <div class="row g-3">
            <?php foreach ($annonces as $annonce): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="<?= BASE_URL ?>/annonce.php?id=<?= $annonce['id'] ?>" class="text-decoration-none text-reset">
                        <div class="card h-100">
                            <?php if ($annonce['image']): ?>
                                <img src="<?= BASE_URL . htmlspecialchars($annonce['image']) ?>"
                                     class="card-img-top" alt="" style="height:130px;object-fit:cover">
                            <?php endif; ?>
                            <div class="card-body pb-2">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                    <h3 class="h6 fw-bold mb-0 nb-upper" style="font-size:.82rem">
                                        <?= htmlspecialchars($annonce['titre']) ?>
                                    </h3>
                                    <?php if ($annonce['prix'] !== null): ?>
                                        <span class="badge bg-success">CHF <?= number_format($annonce['prix'], 0, '.', "'") ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">À discuter</span>
                                    <?php endif; ?>
                                </div>
                                <p class="small mb-0" style="color:#555">
                                    <?= htmlspecialchars(mb_substr($annonce['description'], 0, 80)) ?>…
                                </p>
                            </div>
                            <div class="card-footer">
                                <span class="nb-mono" style="font-size:.7rem;color:#555"><?= date('d.m.Y', strtotime($annonce['created_at'])) ?></span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-end mt-3">
            <a href="<?= BASE_URL ?>/annonces.php" class="btn btn-outline-dark btn-sm">
                Toutes les annonces <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    <?php else: ?>
        <p class="nb-mono" style="color:#555">Aucune annonce pour le moment.</p>
    <?php endif; ?>
</section>

<!-- CTA -->
<section class="mb-5">
    <div style="background:#000;color:#fff;border:var(--nb-border);padding:3rem 2rem;box-shadow:12px 12px 0 0 var(--nb-yellow);text-align:center;position:relative">
        <h3 style="font-size:2rem;font-weight:900;text-transform:uppercase;letter-spacing:-.02em">Rejoignez l'association !</h3>
        <p class="nb-mono mt-2 mb-4" style="color:#aaa;max-width:500px;margin-left:auto;margin-right:auto">
            Participez aux événements, ateliers et projets collaboratifs.<br>Ensemble, développons nos compétences !
        </p>
        <?php if (!isLoggedIn()): ?>
            <a href="<?= BASE_URL ?>/register.php"
               style="background:var(--nb-yellow);color:#000;border:4px solid #fff;padding:.75rem 2rem;font-weight:900;text-transform:uppercase;letter-spacing:.05em;display:inline-block;box-shadow:6px 6px 0 0 #fff;text-decoration:none;transition:box-shadow .1s,transform .1s"
               onmouseover="this.style.boxShadow='3px 3px 0 0 #fff';this.style.transform='translate(3px,3px)'"
               onmouseout="this.style.boxShadow='6px 6px 0 0 #fff';this.style.transform=''"
            >Créer mon compte</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/pubs.php"
               style="background:var(--nb-cyan);color:#000;border:4px solid #fff;padding:.75rem 2rem;font-weight:900;text-transform:uppercase;letter-spacing:.05em;display:inline-block;box-shadow:6px 6px 0 0 #fff;text-decoration:none"
            >Poster une pub</a>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
