document.addEventListener('DOMContentLoaded', () => {
    // --- 1. GESTION DE L'OFFRANDE (PANIER) ---
    const boutonsAjout = document.querySelectorAll('.btn-ajouter, .btn-add-cart');
    const compteurElement = document.getElementById('cart-counter');

    // Récupération du jeton CSRF depuis la balise meta (à ajouter dans ton HTML)
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    boutonsAjout.forEach(bouton => {
        bouton.addEventListener('click', function(evenement) {
            evenement.preventDefault();

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

    // --- 2. ANIMATION DES CARTES (INTERSECTION OBSERVER) ---
    const cards = document.querySelectorAll('.produit-card');
    if (cards.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        cards.forEach(card => observer.observe(card));
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
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    toast.textContent = message;
    toast.style.background = isError ? 'rgba(100, 0, 0, 0.9)' : 'rgba(10, 10, 10, 0.9)';
    toast.style.borderColor = isError ? '#ff4444' : 'var(--gold, #b59410)';
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}