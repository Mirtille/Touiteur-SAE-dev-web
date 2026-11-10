
<h2>Les derniers touites</h2>

<?php if (empty($touites)): ?>

    <p>Aucun touite publié pour le moment.</p>

<?php else: ?>

    <?php foreach ($touites as $touite): ?>

        <article class="touite">

            <div class="touite-header">

                <strong>
                    <?= htmlspecialchars(
                        $touite->auteurNom,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>

                <span>
                    <?= date(
                        'd/m/Y à H:i',
                        strtotime($touite->date)
                    ) ?>
                </span>

            </div>

            <p class="touite-texte">
                <?= htmlspecialchars(
                    $touite->texte,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <div class="touite-footer">

                <span>
                    Score : <?= $touite->score ?>
                </span>

                <a href="index.php?action=detail&id=<?= $touite->id ?>">
                    Voir le détail
                </a>

            </div>

        </article>

    <?php endforeach; ?>

<?php endif; ?>
