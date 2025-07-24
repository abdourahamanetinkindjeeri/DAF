#!/bin/bash
set -e

# Attendre que PostgreSQL soit prêt
until pg_isready -h localhost -p 5432; do
    echo "Waiting for PostgreSQL to be ready..."
    sleep 2
done

# Configurer les permissions et le propriétaire
chown postgres:postgres /etc/postgresql/conf.d/custom.conf
chmod 600 /etc/postgresql/conf.d/custom.conf

# Redémarrer PostgreSQL pour appliquer les configurations
pg_ctl -D /var/lib/postgresql/data restart

echo "PostgreSQL is ready to accept connections" 