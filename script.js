document.addEventListener('DOMContentLoaded', () => {
    // --- 1. GESTION DE L'OFFRANDE (PANIER) ---
    const boutonsAjout = document.querySelectorAll('.btn-ajouter, .btn-add-cart');
    const compteurElement = document.getElementById('cart-counter');

    console.log('Boutons trouvés:', boutonsAjout.length);
    console.log('Toast element:', document.getElementById('toast'));

    // SÉCURITÉ: Rate limiting pour éviter les abus/DOS via AJAX
    let lastAddTime = 0;
    const RATE_LIMIT_MS = 500; // Minimum 500ms entre deux clics

    // Récupération du jeton CSRF depuis la balise meta (à ajouter dans ton HTML)
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    boutonsAjout.forEach(bouton => {
        bouton.addEventListener('click', function(evenement) {
            evenement.preventDefault();
            console.log('Bouton cliqué !');

            // SÉCURITÉ: Vérifier le rate limit
            const currentTime = Date.now();
            if (currentTime - lastAddTime < RATE_LIMIT_MS) {
                showToast("Veuillez patienter avant votre prochaine offrande.", true);
                return;
            }
            lastAddTime = currentTime;

            const idProduit = this.getAttribute('data-id');
            
            let cheminAjouter = 'ajouter_panier.php';
            if (window.location.pathname.includes('/images/')) {
                cheminAjouter = '../ajouter_panier.php';
            }

            // On envoie maintenant l'ID ET le jeton CSRF dans le corps de la requête
            fetch(cheminAjouter, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    id: idProduit,
                    csrf_token: csrfToken 
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Réponse serveur:', data);
                if (data.success) {
                    if (compteurElement) {
                        compteurElement.textContent = data.total;
                        compteurElement.classList.add('pop');
                        setTimeout(() => compteurElement.classList.remove('pop'), 200);
                    }
                    showToast(data.message);
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(erreur => {
                console.error("Erreur lors de l'offrande :", erreur);
                showToast("Une force obscure a empêché l'ajout.", true);
            });
        });
    });

    // --- 2. ANIMATION DES ÉLÉMENTS (INTERSECTION OBSERVER) ---
    // Animation progressive SEULEMENT sur la page cérémonies
    const currentPage = window.location.pathname;
    const isCeremoniesPage = currentPage.includes('ceremonies.php') || currentPage.endsWith('/ceremonies');
    
    if (isCeremoniesPage) {
        // Cherche tous les éléments qui pourraient avoir besoin d'animation
        const elementsToReveal = document.querySelectorAll(
            '.produit-card, .forest-content, .forest-text, .forest-image-box, ' +
            '.foret-split, .foret-text, .foret-image, ' +
            'h2.section-title, h2.cinzel-title, ' +
            '.ceremony, .ritual, [class*="section"]'
        );
        console.log('🎬 Éléments trouvés pour animation:', elementsToReveal.length);
        
        if (elementsToReveal.length > 0) {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        console.log('✨ Élément visible, ajout de la classe visible');
                        entry.target.classList.add('visible');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            elementsToReveal.forEach(el => {
                el.classList.add('reveal');
                revealObserver.observe(el);
            });
        } else {
            console.warn('⚠️ Aucun élément trouvé pour l\'animation');
        }
    }

    // --- 3. BOUTON SCROLL TO TOP ---
    let scrollButton = document.getElementById('scroll-to-top');
    if (!scrollButton) {
        scrollButton = document.createElement('div');
        scrollButton.id = 'scroll-to-top';
        scrollButton.className = 'scroll-to-top';
        scrollButton.innerHTML = '⬆';
        scrollButton.title = 'Remonter vers le haut';
        document.body.appendChild(scrollButton);
    }

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            scrollButton.classList.add('visible');
        } else {
            scrollButton.classList.remove('visible');
        }
    });

    scrollButton.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

// --- 4. FONCTION UTILITAIRE : TOAST ---
function showToast(message, isError = false) {
    console.log('showToast appelé:', message, isError);
    const toast = document.getElementById('toast');
    if (!toast) {
        console.warn('❌ Toast element NOT found!');
        return;
    }
    
    console.log('✅ Toast trouvé, affichage...');
    const icon = isError ? '⚠️' : '✨';
    toast.innerHTML = `<span class="toast-icon">${icon}</span>${message}`;
    toast.style.background = isError ? 'rgba(200, 50, 50, 0.95)' : 'rgba(20, 20, 20, 0.98)';
    toast.style.borderColor = isError ? '#ff6666' : '#D4AF37';
    
    // Force le bottom style directement !
    toast.style.bottom = '30px';
    toast.style.transition = 'bottom 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)';
    
    toast.classList.add('show');
    console.log('Toast bottom set to:', toast.style.bottom);
    console.log('Toast classes:', toast.classList);
    
    setTimeout(() => {
        toast.style.bottom = '-100px';
        toast.classList.remove('show');
    }, 3000);
}