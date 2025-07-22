<?php
// Connexion à la base
$host = 'localhost';
$dbName = 'projetdb';
$user = 'projetuser';
$pass = 'projetpass';

$dsn = "pgsql:host=$host;dbname=$dbName";
try {
  $pdo = new PDO($dsn, $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Insérer des clients
  $pdo->exec("INSERT INTO client (nom, email) VALUES
        ('Client Alpha', 'alpha@example.com'),
        ('Client Beta', 'beta@example.com')
        ON CONFLICT DO NOTHING;");

  // Récupérer les IDs clients
  $stmt = $pdo->query("SELECT id FROM client ORDER BY id ASC");
  $clients = $stmt->fetchAll(PDO::FETCH_COLUMN);

  // Insérer des utilisateurs
  $pdo->exec("INSERT INTO utilisateur (nom, email, type, client_id) VALUES
        ('Alice Admin', 'alice@admin.com', 'admin', {$clients[0]}),
        ('Bob Client', 'bob@client.com', 'client', {$clients[1]})
        ON CONFLICT DO NOTHING;");

  echo "🎉 Données fictives insérées avec succès.\n";
} catch (PDOException $e) {
  echo "❌ Erreur PDO : " . $e->getMessage() . "\n";
  exit(1);
}
