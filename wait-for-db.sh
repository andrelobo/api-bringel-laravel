#!/bin/bash
set -e

host="$1"
shift

echo "Esperando pelo banco de dados ($host)..."
until mysqladmin ping -h "$host" --silent; do
  sleep 2
done

>&2 echo "Banco de dados está pronto!"
exec "$@"
