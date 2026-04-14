<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3 class="footer-logo">LA DERNIÈRE DEMEURE</h3>
            <p class="footer-tagline">L'éternité pour écrin.</p>
        </div>

        <div class="footer-section">
            <h4>Navigation</h4>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="foret.php">Le Sanctuaire</a></li>
                <li><a href="repos_des_fideles.php">Repos des Fidèles</a></li>
                <li><a href="catalogue.php">Le Catalogue</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Informations</h4>
            <ul>
                <li><a href="mentions-legales.php">Mentions Légales</a></li>
                <li><a href="contact.php">Conciergerie</a></li>
                <li><a href="login.php">Espace Gardien</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Contact</h4>
            <p>📍 Villeneuve d’Ascq</p>
            <p>📞 03 20 61 71 51</p>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> La Dernière Demeure - Groupe Incinéris. Tous droits réservés.</p>
    </div>
</footer>

<style>
    .main-footer {
        background: #050505;
        color: #e0e0e0;
        padding: 60px 20px 20px;
        border-top: 1px solid #b59410;
        font-family: 'Cinzel', serif;
        margin-top: 50px;
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 40px;
    }

    .footer-section h4 {
        color: #b59410;
        text-transform: uppercase;
        margin-bottom: 20px;
        font-size: 1.1rem;
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
    }

    .footer-section ul li {
        margin-bottom: 10px;
    }

    .footer-section ul li a {
        color: #aaa;
        text-decoration: none;
        transition: color 0.3s;
    }

    .footer-section ul li a:hover {
        color: #d4af37;
    }

    .footer-logo {
        color: #b59410;
        letter-spacing: 2px;
        margin: 0;
    }

    .footer-tagline {
        font-style: italic;
        color: #777;
        font-size: 0.9rem;
    }

    .footer-bottom {
        text-align: center;
        margin-top: 50px;
        padding-top: 20px;
        border-top: 1px solid #1a1a1a;
        font-size: 0.8rem;
        color: #555;
    }
</style>