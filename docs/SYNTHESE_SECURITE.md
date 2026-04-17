# SYNTHESE_SECURITE - La Derniere Demeure

Mise a jour: 16 avril 2026

## Verdict rapide

- Statut: propre et coherent pour la soutenance.
- Objectif atteint: aucune collecte locale de donnees carte.
- Priorite restante: brancher une vraie cle Stripe si mise en ligne publique.

## Correctifs critiques appliques

1. Paiement local retire (plus de numero/cvv/date sur le site)

- Panier redirige vers un parcours de paiement securise.
- Fichiers: panier.php, create-checkout-session.php, payment-form.php, process-payment.php.
- Ancien endpoint neutralise: traitement_paiement.php redirige vers panier.php.

2. Configuration base de donnees en mode fail-closed

- Plus de fallback implicite root/mot de passe vide.
- Si DB_USER/DB_PASSWORD manquent, l'application s'arrete avec erreur explicite.
- Fichier: config.php.

3. Moderation admin protegee contre CSRF

- Actions de moderation passees en POST + token CSRF.
- Suppression des actions sensibles en GET.
- Fichier: admin.php.

4. Protection path traversal sur modification image

- Validation stricte du chemin manuel dans images/catalogue/.
- Suppression d'ancienne image restreinte au dossier autorise.
- Fichier: modifier.php.

5. Coherence structurelle catalogue/foret

- Correction des chemins et assets sur pages racine.
- Aucun PHP dans le dossier images/.
- Fichiers: catalogue.php, foret.php.

6. Journalisation et headers de securite

- Journalisation d'audit ajoutee pour login, catalogue, moderation, paiement et logout.
- Headers de securite globaux ajoutes dans config.php.
- Logout protegee en POST + CSRF.

## Verification technique

- Syntaxe PHP validee: aucune erreur sur les fichiers modifies.
- CSRF present sur formulaires critiques (login, contact, jardin, admin).
- Rate limiting login actif (helpers.php + login.php).

## Risques restants (non bloquants soutenance, a traiter ensuite)

- Cle Stripe reelle non branchee en production (mode demo possible).
- Journalisation encore volontairement minimale, a enrichir si besoin.

## Message a dire au jury

- Le projet respecte les fondamentaux OWASP: CSRF, sessions, SQL preparees, validation d'entrees.
- Le point PCI-DSS majeur a ete traite: aucune donnee carte n'est collecte/stocker localement.
- Les actions admin sensibles sont protegees en POST + CSRF, avec trace d'audit.

# SYNTHESE_SECURITE - La Derniere Demeure

Mise a jour: 16 avril 2026

## Verdict rapide

- Statut: propre et coherent pour la soutenance.
- Objectif atteint: aucune collecte locale de donnees carte.
- Priorite restante: brancher une vraie cle Stripe si mise en ligne publique.

## Correctifs critiques appliques

1. Paiement local retire (plus de numero/cvv/date sur le site)

- Panier redirige vers un parcours de paiement securise.
- Fichiers: panier.php, create-checkout-session.php, payment-form.php, process-payment.php.
- Ancien endpoint neutralise: traitement_paiement.php redirige vers panier.php.

2. Configuration base de donnees en mode fail-closed

- Plus de fallback implicite root/mot de passe vide.
- Si DB_USER/DB_PASSWORD manquants, l'application s'arrete avec erreur explicite.
- Fichier: config.php.

3. Moderation admin protegee contre CSRF

- Actions de moderation passees en POST + token CSRF.
- Suppression des actions sensibles en GET.
- Fichier: admin.php.

4. Protection path traversal sur modification image

- Validation stricte du chemin manuel dans images/catalogue/.
- Suppression d'ancienne image restreinte au dossier autorise.
- Fichier: modifier.php.

5. Coherence structurelle catalogue/foret

- Correction des chemins et assets sur pages racine.
- Aucun PHP dans le dossier images/.
- Fichiers: catalogue.php, foret.php.

6. Journalisation et headers de securite

- Journalisation d'audit ajoutee pour login, catalogue, moderation, paiement et logout.
- Headers de securite globaux ajoutes dans config.php.
- Logout protegee en POST + CSRF.

## Verification technique

- Syntaxe PHP validee: aucune erreur sur les fichiers modifies.
- CSRF present sur formulaires critiques (login, contact, jardin, admin).
- Rate limiting login actif (helpers.php + login.php).

## Risques restants (non bloquants soutenance, a traiter ensuite)

- Cle Stripe reelle non branchee en production (mode demo possible).
- Journalisation encore volontairement minimale, a enrichir si besoin.

## Message a dire au jury

- Le projet respecte les fondamentaux OWASP: CSRF, sessions, SQL preparees, validation d'entrees.
- Le point PCI-DSS majeur a ete traite: aucune donnee carte n'est collecte/stocker localement.
- Les actions admin sensibles sont protegees en POST + CSRF, avec trace d'audit.
