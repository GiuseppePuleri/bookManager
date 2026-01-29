#!/bin/bash

# Colori e simboli
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

# Simboli Unicode
CHECK="${GREEN}✓${NC}"
CROSS="${RED}✗${NC}"
ARROW="${CYAN}▶${NC}"
INFO="${BLUE}ℹ${NC}"
WARN="${YELLOW}⚠${NC}"

# Nome del container Laravel
CONTAINER_NAME="laravel_app"

# Banner iniziale
clear
echo -e "${BOLD}${CYAN}"
echo "╔════════════════════════════════════════════╗"
echo "║     Book Manager Deployment Automatico     ║"
echo "╚════════════════════════════════════════════╝"
echo -e "${NC}\n"

# Funzione per verificare se un comando esiste
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Funzione per log dettagliato
log_step() {
    echo -e "${ARROW} $1"
}

log_success() {
    echo -e "${CHECK} $1"
}

log_error() {
    echo -e "${CROSS} $1"
}

log_info() {
    echo -e "${INFO} $1"
}

log_warn() {
    echo -e "${WARN} $1"
}

# Funzione per installare Docker (SENZA aggiornamento repository)
install_docker() {
    log_step "Installazione Docker in corso..."
    
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        if command_exists apt-get; then
            log_info "Sistema: Debian/Ubuntu"
            
            # SALTA l'aggiornamento completo, installa solo le dipendenze base
            log_step "  • Installazione dipendenze base (senza update)..."
            sudo apt-get install -y --no-install-recommends ca-certificates curl gnupg lsb-release 2>> /tmp/docker_install.log
            
            if [ $? -eq 0 ]; then
                log_success "    Dipendenze base installate"
            else
                log_warn "    Alcune dipendenze potrebbero mancare, procedo comunque..."
            fi
            
            log_step "  • Aggiunta chiave GPG Docker..."
            sudo mkdir -p /etc/apt/keyrings
            curl -fsSL https://download.docker.com/linux/ubuntu/gpg 2>/dev/null | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg 2>/dev/null
            [ $? -eq 0 ] && log_success "    Chiave GPG aggiunta" || { log_error "    Errore aggiunta chiave GPG"; exit 1; }
            
            log_step "  • Configurazione repository Docker..."
            echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
            [ $? -eq 0 ] && log_success "    Repository Docker configurato" || { log_error "    Errore configurazione repository"; exit 1; }
            
            log_step "  • Aggiornamento SOLO repository Docker..."
            sudo apt-get update -o Dir::Etc::sourcelist="sources.list.d/docker.list" -o Dir::Etc::sourceparts="-" -o APT::Get::List-Cleanup="0" 2>/dev/null
            [ $? -eq 0 ] && log_success "    Repository Docker aggiornato" || log_warn "    Procedo comunque..."
            
            log_step "  • Installazione Docker Engine (può richiedere alcuni minuti)..."
            sudo apt-get install -y --allow-unauthenticated docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin 2>> /tmp/docker_install.log
            
            if [ $? -eq 0 ]; then
                log_success "    Docker Engine installato"
            else
                log_error "    Errore installazione Docker Engine"
                log_info "Dettagli in /tmp/docker_install.log"
                exit 1
            fi
            
        elif command_exists yum; then
            log_info "Sistema: CentOS/RHEL"
            
            log_step "  • Installazione yum-utils..."
            sudo yum install -y -q yum-utils >> /tmp/docker_install.log 2>&1
            [ $? -eq 0 ] && log_success "    yum-utils installato" || { log_error "    Errore installazione yum-utils"; exit 1; }
            
            log_step "  • Aggiunta repository Docker..."
            sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo >> /tmp/docker_install.log 2>&1
            [ $? -eq 0 ] && log_success "    Repository Docker aggiunto" || { log_error "    Errore aggiunta repository"; exit 1; }
            
            log_step "  • Installazione Docker..."
            sudo yum install -y -q docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin >> /tmp/docker_install.log 2>&1
            [ $? -eq 0 ] && log_success "    Docker installato" || { log_error "    Errore installazione Docker"; exit 1; }
            
            log_step "  • Avvio servizio Docker..."
            sudo systemctl start docker
            sudo systemctl enable docker
            [ $? -eq 0 ] && log_success "    Servizio Docker avviato" || { log_error "    Errore avvio servizio Docker"; exit 1; }
        else
            log_error "Distribuzione Linux non supportata"
            exit 1
        fi
        
        log_step "  • Configurazione permessi utente..."
        sudo usermod -aG docker $USER > /dev/null 2>&1
        [ $? -eq 0 ] && log_success "    Permessi configurati" || log_warn "    Errore configurazione permessi (non critico)"
        
        log_step "  • Avvio servizio Docker..."
        sudo systemctl start docker 2>/dev/null
        sudo systemctl enable docker 2>/dev/null
        sleep 2
        
        log_success "Docker installato con successo"
        echo ""
        log_warn "${YELLOW}IMPORTANTE: Applica i permessi Docker:${NC}"
        echo -e "  ${CYAN}newgrp docker${NC}"
        echo ""
        log_info "Poi riesegui questo script"
        exit 0
        
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        log_info "Scarica Docker Desktop da: https://www.docker.com/products/docker-desktop"
        exit 1
    else
        log_error "Sistema operativo non supportato"
        exit 1
    fi
}

# Funzione per installare docker-compose
install_docker_compose() {
    log_step "Installazione docker-compose..."
    
    log_step "  • Download docker-compose..."
    sudo curl -sL "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose 2>/dev/null
    [ $? -eq 0 ] && log_success "    Download completato" || { log_error "    Errore download"; exit 1; }
    
    log_step "  • Impostazione permessi..."
    sudo chmod +x /usr/local/bin/docker-compose
    [ $? -eq 0 ] && log_success "    Permessi impostati" || { log_error "    Errore permessi"; exit 1; }
    
    log_success "docker-compose installato"
}

# Verifica Docker
echo -e "${BOLD}${BLUE}═══ Fase 1: Verifica Requisiti ═══${NC}\n"
log_step "Verifica installazione Docker..."

if ! command_exists docker; then
    log_error "Docker non trovato"
    read -p "   Installare Docker? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        install_docker
    else
        log_error "Operazione annullata"
        exit 1
    fi
else
    DOCKER_VERSION=$(docker --version 2>/dev/null | cut -d' ' -f3 | tr -d ',')
    log_success "Docker trovato: v${DOCKER_VERSION}"
fi

# Verifica che Docker sia avviato
log_step "Verifica servizio Docker..."
if ! docker info >/dev/null 2>&1; then
    log_warn "Docker non è avviato o non hai i permessi"
    
    # Verifica se è un problema di permessi
    if groups | grep -q docker; then
        log_step "  • Tentativo avvio Docker..."
        sudo systemctl start docker 2>/dev/null
        sleep 2
        if docker info >/dev/null 2>&1; then
            log_success "    Docker avviato"
        else
            log_error "    Impossibile avviare Docker"
            log_info "Prova: sudo systemctl status docker"
            exit 1
        fi
    else
        log_error "L'utente '$(whoami)' non è nel gruppo 'docker'"
        echo ""
        log_info "Esegui questo comando e poi riesegui lo script:"
        echo -e "  ${CYAN}newgrp docker${NC}"
        exit 1
    fi
else
    log_success "Servizio Docker attivo"
fi

# Verifica docker-compose
log_step "Verifica installazione Docker Compose..."
if command_exists docker-compose; then
    COMPOSE_VERSION=$(docker-compose --version 2>/dev/null | cut -d' ' -f4 | tr -d ',')
    log_success "Docker Compose trovato: v${COMPOSE_VERSION}"
    COMPOSE_CMD="docker-compose"
elif docker compose version >/dev/null 2>&1; then
    COMPOSE_VERSION=$(docker compose version --short 2>/dev/null)
    log_success "Docker Compose trovato: v${COMPOSE_VERSION}"
    COMPOSE_CMD="docker compose"
else
    log_error "docker-compose non trovato"
    read -p "   Installare docker-compose? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        install_docker_compose
        COMPOSE_CMD="docker-compose"
    else
        log_error "Operazione annullata"
        exit 1
    fi
fi

# Verifica file docker-compose.yml
log_step "Verifica file docker-compose.yml..."
if [ ! -f "docker-compose.yml" ]; then
    log_error "File docker-compose.yml non trovato nella directory corrente"
    log_info "Directory corrente: $(pwd)"
    exit 1
fi
log_success "File docker-compose.yml trovato"

# Avvio container
echo -e "\n${BOLD}${BLUE}═══ Fase 2: Avvio Container ═══${NC}\n"
log_step "Build e avvio container..."
log_info "Questo processo potrebbe richiedere alcuni minuti..."
echo ""

$COMPOSE_CMD up -d --build 2>&1 | while IFS= read -r line; do
    echo "  $line"
done

if [ ${PIPESTATUS[0]} -eq 0 ]; then
    echo ""
    log_success "Container avviati con successo"
else
    echo ""
    log_error "Errore nell'avvio dei container"
    exit 1
fi

# Attesa stabilizzazione container
log_step "Attesa stabilizzazione container..."
for i in {10..1}; do
    echo -ne "   $i secondi...\r"
    sleep 1
done
echo -e "  ${CHECK} Container stabilizzati     "

# Verifica container
echo -e "\n${BOLD}${BLUE}═══ Fase 3: Verifica Container ═══${NC}\n"
log_step "Verifica stato container..."

if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
    log_error "Container '$CONTAINER_NAME' non trovato o non in esecuzione"
    echo ""
    log_info "Container in esecuzione:"
    docker ps --format "  • {{.Names}} - {{.Status}}" || echo "  Nessun container in esecuzione"
    echo ""
    log_info "Tutti i container:"
    docker ps -a --format "  • {{.Names}} - {{.Status}}"
    exit 1
fi

log_success "Container '$CONTAINER_NAME' in esecuzione"

# Mostra informazioni container
CONTAINER_STATUS=$(docker ps --filter "name=${CONTAINER_NAME}" --format "{{.Status}}")
log_info "Status: ${CONTAINER_STATUS}"


# Preparazione e popolamento database
echo -e "\n${BOLD}${BLUE}═══ Fase 4: Preparazione Database ═══${NC}\n"

# Migrate fresh
log_step "Ricreazione database (migrate:fresh)..."
docker exec -it $CONTAINER_NAME php artisan migrate:fresh 2>&1 | while IFS= read -r line; do
    echo "  $line"
done

if [ ${PIPESTATUS[0]} -ne 0 ]; then
    echo ""
    log_error "Errore durante migrate:fresh"
    exit 1
fi
echo ""
log_success "Database ricreato con successo"

# Key generate
log_step "Generazione chiave applicazione..."
docker exec -it $CONTAINER_NAME php artisan key:generate 2>&1 | while IFS= read -r line; do
    echo "  $line"
done

if [ ${PIPESTATUS[0]} -ne 0 ]; then
    echo ""
    log_error "Errore durante key:generate"
    exit 1
fi
echo ""
log_success "Chiave applicazione generata"

# Seeder
log_step "Esecuzione seeder del database..."
log_info "Popolamento con dati di test in corso..."
echo ""

docker exec -it $CONTAINER_NAME php artisan db:seed --class=Database\\Seeders\\FakeDataSeeder 2>&1 | while IFS= read -r line; do
    echo "  $line"
done

if [ ${PIPESTATUS[0]} -eq 0 ]; then
    echo ""
    log_success "Database popolato con successo"
else
    echo ""
    log_error "Errore durante il seeding del database"
    echo ""
    log_info "Suggerimenti:"
    echo -e "  • Verifica i log: ${CYAN}docker logs $CONTAINER_NAME${NC}"
    echo -e "  • Controlla il database: ${CYAN}docker exec -it $CONTAINER_NAME php artisan migrate:status${NC}"
    exit 1
fi

# Riepilogo finale
echo -e "\n${BOLD}${GREEN}╔════════════════════════════════════════════╗${NC}"
echo -e "${BOLD}${GREEN}║           Deployment Completato!           ║${NC}"
echo -e "${BOLD}${GREEN}╚════════════════════════════════════════════╝${NC}\n"

echo -e "${BOLD}${CYAN}╔════════════════════════════════════════════╗${NC}"
echo -e "${BOLD}${CYAN}║     Applicazione disponibile su:           ║${NC}"
echo -e "${BOLD}${CYAN}║        http://localhost:8080               ║${NC}"
echo -e "${BOLD}${CYAN}╚════════════════════════════════════════════╝${NC}\n"

echo -e "${INFO} ${BOLD}Comandi utili:${NC}"
echo -e "  ${CYAN} Logs:${NC}        docker logs -f $CONTAINER_NAME"
echo -e "  ${CYAN} Stop:${NC}        $COMPOSE_CMD down"
echo -e "  ${CYAN} Restart:${NC}     $COMPOSE_CMD restart"
echo -e "  ${CYAN} Shell:${NC}       docker exec -it $CONTAINER_NAME bash"
echo -e "  ${CYAN} Cleanup:${NC}    $COMPOSE_CMD down -v"
echo ""