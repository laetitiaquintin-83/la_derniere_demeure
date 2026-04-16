<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
		http_response_code(403);
		die('Erreur de sécurité : jeton CSRF invalide.');
	}

	log_audit_event('LOGOUT', 'admin_auth', null, ['admin_connecte' => true], ['admin_connecte' => false]);

	session_unset();
	session_destroy();

	header('Location: index.php');
	exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Déconnexion | La Dernière Demeure</title>
	<link rel="stylesheet" href="style.css">
</head>
<body class="admin-body">
	<div class="admin-container" style="max-width: 520px; text-align: center;">
		<h1 class="admin-title">Confirmer la Déconnexion</h1>
		<p style="color: #cfcfcf; line-height: 1.7; margin-bottom: 28px;">
			Vous êtes sur le point de quitter le registre. Confirmez l'opération pour fermer la session du gardien.
		</p>
		<form method="POST">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(genererTokenCSRF()); ?>">
			<button type="submit" class="btn-crypt">Se Déconnecter</button>
		</form>
		<div style="margin-top: 18px;">
			<a href="admin.php" style="color: #888; text-decoration: none;">✦ Retour</a>
		</div>
	</div>
</body>
</html>