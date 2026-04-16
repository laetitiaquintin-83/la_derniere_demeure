# CHECKLIST_EXAMEN - Version finale (16 avril 2026)

## 1) Securite applicative
- [x] SQL: requetes preparees PDO
- [x] CSRF: tokens verifies sur formulaires sensibles
- [x] Session: regeneration apres login
- [x] Login: rate limiting actif
- [x] Uploads: controle extension + MIME + taille
- [x] Moderation admin: actions sensibles en POST (pas en GET)
- [x] Paiement: aucune saisie carte locale

## 2) Paiement
- [x] panier.php -> create-checkout-session.php
- [x] create-checkout-session.php -> payment-form.php
- [x] payment-form.php -> process-payment.php
- [x] traitement_paiement.php desactive (redirection)
- [x] Stock decremente en transaction

## 3) Configuration
- [x] Variables .env chargees
- [x] DB_USER et DB_PASSWORD obligatoires (fail-closed)
- [ ] Cle Stripe production a renseigner si mise en ligne reelle

## 4) Demonstration jury (10 minutes de preuve)
1. Login admin (tentatives rate-limit)
2. Ajout produit au panier
3. Passage au paiement securise
4. Confirmation commande et decrement stock
5. Moderation message jardin (POST + CSRF)

## 5) Questions probables du jury (reponses courtes)
- Pourquoi abandon du formulaire carte local ?
  - Parce que PCI-DSS: aucune donnee carte ne doit transiter sur notre serveur.
- Comment eviter CSRF ?
  - Token aleatoire en session + verification hash_equals.
- Comment eviter SQL injection ?
  - Requetes preparees PDO uniquement.
- Que se passe-t-il si config DB est absente ?
  - L'application bloque le demarrage (fail-closed).
