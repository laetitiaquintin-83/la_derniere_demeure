# Plan du projet (clair et rapide)

Objectif: comprendre ou modifier le projet sans se perdre.

## 1) Ou toucher selon le besoin

- Page visible (UI): public/pages/client, public/pages/admin, public/pages/payment, public/pages/info
- Logique de page: app/Controllers/
- Acces base de donnees: app/Models/
- Point d'entree URL: fichiers .php dans public/pages/* (repartis par usage)
- Configuration et securite: app/Config/config.local.php, app/Config/, app/bootstrap.php
- Partiels de vue (footer, etc.): app/Views/partials/
- Assets: style.css, script.js, images/
- Script SQL: database/DATABASE_SETUP.sql
- Endpoints techniques: public/endpoints/
- Documentation: docs/
- Outils locaux: tools/

## 2) Flux MVC actuel

1. URL appelee (ex: index.php)
2. Bootstrap charge (app/bootstrap.php)
3. Controller execute la logique
4. Model recupere les donnees
5. View affiche le HTML

## 3) Pages clefs (racine)

- Public: index.php, catalogue.php, panier.php, contact.php, foret.php, ceremonies.php, repos_des_fideles.php, mentions-legales.php
- Auth/Admin: login.php, logout.php, admin.php, gestion.php, modifier.php, supprimer.php
- Paiement: payment-form.php, payment-success.php, create-checkout-session.php, process-payment.php
- Endpoints techniques: ajouter_panier.php, update_stock.php, traitement_jardin.php, traitement_paiement.php

## 4) Regles de rangement

- Garder les URL publiques accessibles via la racine, servies depuis public/ avec rewrite
- Mettre le HTML de page dans app/Views/pages/
- Eviter la logique SQL dans les vues
- Ranger les rapports et livrables uniquement dans docs/
- Ranger les scripts utilitaires uniquement dans tools/
