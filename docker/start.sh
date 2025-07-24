#!/bin/bash
set -e

echo "Starting PostgreSQL initialization..."

# Créer le répertoire des données s'il n'existe pas
mkdir -p /var/lib/postgresql/data
chown -R postgres:postgres /var/lib/postgresql/data
chmod 700 /var/lib/postgresql/data

# Afficher les variables d'environnement (sans les mots de passe)
echo "Environment configuration:"
echo "POSTGRES_DB: $POSTGRES_DB"
echo "POSTGRES_USER: $POSTGRES_USER"
echo "PGDATA: $PGDATA"

# Vérifier les fichiers de configuration
echo "Checking configuration files..."
ls -la /etc/postgresql/
cat /etc/postgresql/postgresql.conf

# S'assurer que le fichier de configuration appartient à postgres
chown postgres:postgres /etc/postgresql/postgresql.conf
chmod 600 /etc/postgresql/postgresql.conf

echo "Starting PostgreSQL..."
# Exécuter PostgreSQL en tant qu'utilisateur postgres
exec gosu postgres postgres \
    -c config_file=/etc/postgresql/postgresql.conf \
    -c listen_addresses='*' \
    -c port=5432 \
    -c log_min_messages=debug1 