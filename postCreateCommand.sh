#! /usr/bin/env bash
echo "Starting post container create command..."
php bin/cake.php post_deployment
exec apache2-foreground
