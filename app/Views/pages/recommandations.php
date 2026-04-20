<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
    <title>Livre de Confiance | La Derniere Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Cormorant+Garamond:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .trust-hero {
            position: relative;
            min-height: 56vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
            background: #050505;
            overflow: hidden;
        }
        .trust-hero img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            background: #050505;
        }
        .trust-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(140deg, rgba(0, 0, 0, 0.65), rgba(20, 35, 20, 0.45));
            z-index: 1;
        }
        .trust-hero-content {
            position: relative;
            z-index: 2;
        }
        .trust-hero h1 {
            font-family: 'Cinzel', serif;
            font-size: clamp(2rem, 6vw, 3.6rem);
            letter-spacing: 0.08em;
            color: #f3ebd1;
            margin: 0;
            text-shadow: 0 8px 24px rgba(0, 0, 0, 0.8);
        }
        .trust-hero p {
            margin-top: 18px;
            color: #e4d7b3;
            font-size: clamp(1rem, 2.2vw, 1.3rem);
            max-width: 760px;
        }
        .trust-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 44px 20px 80px;
            display: grid;
            gap: 30px;
            grid-template-columns: 1.2fr 1fr;
        }
        .trust-list,
        .trust-form {
            background: linear-gradient(180deg, rgba(10, 18, 10, 0.92), rgba(0, 0, 0, 0.95));
            border: 1px solid rgba(190, 167, 96, 0.35);
            border-radius: 14px;
            padding: 24px;
        }
        .section-title {
            margin: 0 0 16px;
            color: #d8be72;
            font-family: 'Cinzel', serif;
            letter-spacing: 0.06em;
            font-size: 1.2rem;
            text-transform: uppercase;
        }
        .cards {
            display: grid;
            gap: 14px;
        }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(212, 175, 55, 0.16);
            border-radius: 10px;
            padding: 14px;
        }
        .card-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            margin-bottom: 8px;
        }
        .card-name {
            color: #f6edcf;
            font-weight: 600;
        }
        .card-service {
            color: #b6d8b6;
            font-size: 0.9rem;
        }
        .card-message {
            color: #d8d8d8;
            line-height: 1.6;
            margin: 0;
        }
        .card-date {
            color: #9f9f9f;
            font-size: 0.8rem;
            margin-top: 10px;
        }
        .trust-form label {
            display: block;
            margin-bottom: 7px;
            color: #d8be72;
            font-size: 0.86rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .trust-form input,
        .trust-form textarea,
        .trust-form select {
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 14px;
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.45);
            color: #f4f4f4;
            padding: 11px 12px;
        }
        .trust-form textarea {
            min-height: 130px;
            resize: vertical;
        }
        .consent {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            color: #d3d3d3;
            font-size: 0.92rem;
            margin-bottom: 12px;
        }
        .consent input {
            margin-top: 4px;
            width: auto;
        }
        .btn-trust {
            border: none;
            border-radius: 999px;
            padding: 12px 18px;
            background: linear-gradient(120deg, #d4af37, #9c8640);
            color: #121212;
            font-family: 'Cinzel', serif;
            letter-spacing: 0.06em;
            cursor: pointer;
            width: 100%;
            font-weight: 700;
        }
        .flash {
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }
        .flash.success {
            border: 1px solid rgba(126, 214, 145, 0.4);
            color: #b6f0c1;
            background: rgba(44, 96, 58, 0.25);
        }
        .flash.error {
            border: 1px solid rgba(235, 112, 112, 0.4);
            color: #f8b2b2;
            background: rgba(80, 26, 26, 0.3);
        }
        .empty-state {
            color: #b9b9b9;
            font-style: italic;
            border: 1px dashed rgba(212, 175, 55, 0.26);
            border-radius: 10px;
            padding: 16px;
        }
        @media (max-width: 980px) {
            .trust-wrap {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body style="margin: 0; background: #050505; color: #f3ebd1;">
    <header style="background: rgba(0,0,0,0.9); padding: 15px 5%; border-bottom: 1px solid #333;">
        <nav>
            <a href="index.php">✦ Accueil</a>
            <a href="catalogue.php">✿ Le Catalogue</a>
            <a href="foret.php">✾ Le Sanctuaire</a>
            <a href="ceremonies.php">❦ L'Art de l'Adieu</a>
            <a href="recommandations.php" class="active" style="color: #D4AF37;">✧ Livre de Confiance</a>
            <a href="contact.php">❋ Conciergerie</a>
            <a href="panier.php" style="margin-left: auto;">✵ L'Offrande <span id="cart-counter"><?php echo (int) $nombre_articles; ?></span></a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="lock-link">◇ Quitter</a>
            <?php else: ?>
                <a href="login.php" class="lock-link">◆ Entrer</a>
            <?php endif; ?>
        </nav>
    </header>

    <section class="trust-hero">
        <img src="<?php echo htmlspecialchars($hero_image); ?>" alt="Hommage en foret">
        <div class="trust-hero-content">
            <h1>Ils nous ont confie l'essentiel</h1>
            <p>Chaque message depose ici raconte un accompagnement humain, discret et digne. Merci pour votre confiance.</p>
        </div>
    </section>

    <main class="trust-wrap">
        <section class="trust-list">
            <h2 class="section-title">Temoignages recents</h2>
            <div class="cards">
                <?php if (empty($temoignages)): ?>
                    <div class="empty-state">Aucun temoignage pour le moment. Soyez le premier a partager votre experience.</div>
                <?php else: ?>
                    <?php foreach ($temoignages as $temoignage): ?>
                        <article class="card">
                            <div class="card-top">
                                <span class="card-name"><?php echo htmlspecialchars($temoignage['nom']); ?></span>
                                <span class="card-service"><?php echo htmlspecialchars($temoignage['service']); ?></span>
                            </div>
                            <p class="card-message"><?php echo nl2br(htmlspecialchars($temoignage['message'])); ?></p>
                            <div class="card-date"><?php echo htmlspecialchars(date('d/m/Y', strtotime((string) $temoignage['created_at']))); ?></div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="trust-form">
            <h2 class="section-title">Partager votre confiance</h2>

            <?php if ($flash_message !== ''): ?>
                <div class="flash <?php echo htmlspecialchars($flash_type); ?>"><?php echo htmlspecialchars($flash_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="recommandations.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <label for="nom">Nom et prenom</label>
                <input id="nom" name="nom" type="text" maxlength="120" required value="<?php echo htmlspecialchars($form_data['nom']); ?>">

                <label for="email">Email</label>
                <input id="email" name="email" type="email" maxlength="255" required value="<?php echo htmlspecialchars($form_data['email']); ?>">

                <label for="service">Service concerne</label>
                <select id="service" name="service" required>
                    <option value="">Selectionner</option>
                    <?php
                    $services = [
                        'Conciergerie funeraires',
                        'Choix du cercueil ou urne',
                        'Ceremonie personnalisee',
                        'Foret cineraire',
                        'Accompagnement administratif',
                    ];
                    foreach ($services as $service):
                    ?>
                        <option value="<?php echo htmlspecialchars($service); ?>" <?php echo ($form_data['service'] === $service) ? 'selected' : ''; ?>><?php echo htmlspecialchars($service); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="message">Votre recommandation</label>
                <textarea id="message" name="message" maxlength="1200" required><?php echo htmlspecialchars($form_data['message']); ?></textarea>

                <label class="consent" for="consentement">
                    <input id="consentement" name="consentement" type="checkbox" value="1" required>
                    <span>J'autorise la publication de mon temoignage sur le site.</span>
                </label>

                <button type="submit" class="btn-trust">Publier mon temoignage</button>
            </form>
        </section>
    </main>

    <script src="script.js"></script>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
