#!/usr/bin/env bash
#
# pg-tunnel.sh — throwaway socat bridge to reach the Postgres container from a
# DataGrip/psql SSH tunnel, WITHOUT publishing the DB port in compose.
#
# Binds host 127.0.0.1:<PORT> -> pgsql:5432 over the backend Docker network.
# Loopback-only, so it's reachable through your SSH tunnel but not the internet.
# The container uses --rm, so `stop` (or a host reboot) leaves nothing behind.
#
# Usage (run on the server):
#   scripts/pg-tunnel.sh start     # start the bridge (default port 5432)
#   scripts/pg-tunnel.sh stop      # remove the bridge
#   scripts/pg-tunnel.sh status    # show whether it's running
#
# Env overrides:
#   PORT=15432   scripts/pg-tunnel.sh start   # if 5432 is taken locally
#   NETWORK=...  scripts/pg-tunnel.sh start   # if the backend net has another name
#   TARGET=...   scripts/pg-tunnel.sh start   # target host:port (default pgsql:5432)

set -euo pipefail

NAME="pg-tunnel"
PORT="${PORT:-5432}"
NETWORK="${NETWORK:-check-in_backend}"
TARGET="${TARGET:-pgsql:5432}"

start() {
    if docker ps --format '{{.Names}}' | grep -qx "${NAME}"; then
        echo "${NAME} already running (127.0.0.1:${PORT} -> ${TARGET})"
        return 0
    fi

    if ! docker network inspect "${NETWORK}" >/dev/null 2>&1; then
        echo "error: docker network '${NETWORK}' not found. Available:" >&2
        docker network ls --format '  {{.Name}}' | grep -i backend >&2 || docker network ls >&2
        echo "Re-run with: NETWORK=<name> $0 start" >&2
        return 1
    fi

    docker run --rm -d --name "${NAME}" \
        --network "${NETWORK}" \
        -p "127.0.0.1:${PORT}:5432" \
        alpine/socat \
        "tcp-listen:5432,fork,reuseaddr" "tcp-connect:${TARGET}" >/dev/null

    echo "${NAME} up: 127.0.0.1:${PORT} -> ${TARGET} (network ${NETWORK})"
    echo "Point DataGrip's SSH tunnel at host 127.0.0.1, port ${PORT}."
}

stop() {
    if docker ps -a --format '{{.Names}}' | grep -qx "${NAME}"; then
        docker rm -f "${NAME}" >/dev/null
        echo "${NAME} removed."
    else
        echo "${NAME} not running."
    fi
}

status() {
    if docker ps --format '{{.Names}}\t{{.Ports}}' | grep -q "^${NAME}\b"; then
        docker ps --filter "name=^${NAME}$" --format '{{.Names}} up: {{.Ports}}'
    else
        echo "${NAME} not running."
    fi
}

case "${1:-}" in
    start)  start ;;
    stop)   stop ;;
    status) status ;;
    *)
        echo "Usage: $0 {start|stop|status}" >&2
        exit 1
        ;;
esac
