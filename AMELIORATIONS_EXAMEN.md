# AMELIORATIONS_EXAMEN - Etat final utile jury
Mise a jour: 16 avril 2026

## Fait
- Paiement: abandon de la collecte locale des donnees carte.
- Security by default: actions admin critiques protegees en POST + CSRF.
- Configuration: blocage si credentials DB absents.
- Uploads/modification image: validation chemin stricte.
- Coherence des pages racine (catalogue + foret) corrigee.

## A montrer pendant la soutenance
1. Login admin + protection brute-force.
2. Ajout au panier + passage vers paiement securise.
3. Confirmation de commande + mise a jour stock.
4. Moderation jardin avec validation CSRF.

## A annoncer comme ameliorations futures
- En-tetes HTTP de securite centralises.
- Logs d'audit securite plus complets.
- Monitoring simple (erreurs + evenements critiques).
