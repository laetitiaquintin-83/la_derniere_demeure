-- ====================================================
-- 🔐 SCRIPT SÉCURISATION BASE DE DONNÉES
-- ====================================================
-- 
-- À exécuter UNE SEULE FOIS via phpMyAdmin ou console MySQL
-- Cela crée un utilisateur dédié avec permissions limitées
--
-- ====================================================

-- 1. CRÉER L'UTILISATEUR DÉDIÉ (S'IL N'EXISTE PAS)
-- ====================================================
CREATE USER IF NOT EXISTS 'demeure_user'@'localhost' IDENTIFIED BY 'VotreMotDePasseForte123!@#';

-- OU SI L'UTILISATEUR EXISTE DÉJÀ, MODIFIER SON PASSWORD:
-- ALTER USER 'demeure_user'@'localhost' IDENTIFIED BY 'VotreMotDePasseForte123!@#';

-- 2. DONNER LES PERMISSIONS NÉCESSAIRES (UNIQUEMENT ce qui est nécessaire)
-- ====================================================
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX 
ON la_derniere_demeure.* 
TO 'demeure_user'@'localhost';

-- 3. APPLIQUER LES CHANGEMENTS
-- ====================================================
FLUSH PRIVILEGES;

-- 4. VÉRIFIER QUE L'UTILISATEUR EST CRÉÉ
-- ====================================================
SELECT User, Host FROM mysql.user WHERE User = 'demeure_user';

-- ====================================================
-- CONFIGURATION
-- ====================================================
-- 
-- • Mets à jour le fichier .env avec :
--   DB_USER=demeure_user
--   DB_PASSWORD=VotreMotDePasseForte123!@#
--
-- • IMPORTANT: Utilise un VRAI mot de passe fort en production !
--   Exemple de mot de passe FORT:
--   - Au moins 12 caractères
--   - Majuscules + minuscules + chiffres + symboles
--   - Pas de mots du dictionnaire
--   - Pas d'infos personnelles
--
-- • Générer un mot de passe sécurisé:
--   Linux/Mac: openssl rand -base64 16
--   Windows: [System.Convert]::ToBase64String([System.Security.Cryptography.RandomNumberGenerator]::GetBytes(16))
--
-- ====================================================
-- APRÈS EXÉCUTION
-- ====================================================
--
-- 1. Vérifie que .env contient les bonnes infos
-- 2. Test la connexion en visitant la page d'accueil
-- 3. Supprime l'utilisateur 'root' ou change son password aussi
--
-- ====================================================
