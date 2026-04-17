# GUIDE_CORRECTION_RAPIDE - État réel du projet

Mise à jour: 16 avril 2026

## Ce qui a été corrigé

- Paiement local retiré.
- Endpoint legacy `traitement_paiement.php` neutralisé.
- Configuration DB durcie, sans fallback root vide.
- Modération admin migrée de GET vers POST + CSRF.
- Déconnexion protégée en POST + CSRF.
- Validation stricte des chemins image dans `modifier.php`.
- Chemins du catalogue et de la forêt remis en cohérence à la racine.
- Aucun PHP dans le dossier `images/`.
- Journalisation d'audit et headers de sécurité ajoutés.

## Fichiers principaux concernés

- [config.php](config.php)
- [helpers.php](helpers.php)
- [login.php](login.php)
- [logout.php](logout.php)
- [admin.php](admin.php)
- [modifier.php](modifier.php)
- [panier.php](panier.php)
- [create-checkout-session.php](create-checkout-session.php)
- [payment-form.php](payment-form.php)
- [process-payment.php](process-payment.php)
- [catalogue.php](catalogue.php)
- [foret.php](foret.php)
- [traitement_paiement.php](traitement_paiement.php)

## Vérifications à lancer avant soutenance

1. `php -l` sur tous les fichiers modifiés.
2. Test login avec blocage après plusieurs échecs.
3. Test panier -> paiement -> confirmation.
4. Test modération admin (valider / supprimer).
5. Test formulaire contact + repos des fidèles (CSRF).
6. Vérifier qu'aucun lien ne pointe vers `/images/*.php`.

## Limites connues à assumer devant le jury

- Le paiement reste en mode démo tant qu'une vraie clé Stripe n'est pas branchée.
- La journalisation est volontairement légère, mais elle existe.
