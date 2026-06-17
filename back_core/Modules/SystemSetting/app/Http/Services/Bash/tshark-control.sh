#!/bin/bash

# Configuration
PROCESS_NAME="tshark"
TSHARK_PATH=$(which tshark)
#LOG_DIR="/tmp/tshark/"
#LOG_DIR="/home/bbdh/Desktop/trace/log/"
LOG_DIR="/var/log/bbdh/"
LOG_FILE="${LOG_DIR}tshark-control.log"
IP=$(hostname -I | awk '{print $1}')
DEFAULT_INTERFACE="any"

# ---------------------- Logging Functions ----------------------
log() {
    if [ ! -d "$LOG_DIR" ]; then
        mkdir -p "$LOG_DIR"
        chmod 777 "$LOG_DIR"
    fi

    if [ ! -f "$LOG_FILE" ]; then
        touch "$LOG_FILE"
        chmod 777 "$LOG_FILE"
    fi

    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# ---------------------- Interface Management ----------------------
get_available_interfaces() {
    "$TSHARK_PATH" -D 2>/dev/null
}

validate_interface() {
    local interface=$1
    local interface_list
    interface_list=$(get_available_interfaces)

    if echo "$interface_list" | grep -q -E "^[0-9]+\. $interface$"; then
        return 0
    else
        return 1
    fi
}

# ---------------------- Port Discovery ----------------------
get_ports_by_services() {
    local services=("$@")
    local -a ports=()

    for service in "${services[@]}"; do
        local found=false

        log "Looking for ports for service/process: $service"

        # Method 1: Check /etc/services
        local service_port
        service_port=$(grep -E "^$service[[:space:]]+" /etc/services 2>/dev/null | head -1 | awk '{print $2}' | cut -d'/' -f1)

        if [[ -n "$service_port" ]]; then
            found=true
            if [[ ! " ${ports[*]} " =~ " $service_port " ]]; then
                ports+=("$service_port")
                log "Found port $service_port for service '$service' in /etc/services"
            fi
        fi

        # Method 2: Check ss output
        local port_lines_ss
        port_lines_ss=$(ss -tulnp 2>/dev/null | grep "$service")

        if [[ -n "$port_lines_ss" ]]; then
            found=true
            while IFS= read -r line; do
                extracted_ports=$(echo "$line" | awk '{print $5}' | awk -F':' '{print $NF}')
                for port in $extracted_ports; do
                    if [[ "$port" =~ ^[0-9]+$ && ! " ${ports[*]} " =~ " $port " ]]; then
                        ports+=("$port")
                        log "Found port $port for process '$service' in ss output"
                    fi
                done
            done <<< "$port_lines_ss"
        fi

        # Method 3: Check lsof output
        local port_lines_lsof
        port_lines_lsof=$(lsof -i -P -n 2>/dev/null | grep "$service")

        if [[ -n "$port_lines_lsof" ]]; then
            found=true
            while IFS= read -r line; do
                extracted_ports=$(echo "$line" | awk '{print $9}' | grep -oE ':[0-9]+' | cut -d':' -f2 | sort -u)
                for port in $extracted_ports; do
                    if [[ "$port" =~ ^[0-9]+$ && ! " ${ports[*]} " =~ " $port " ]]; then
                        ports+=("$port")
                        log "Found port $port for process '$service' in lsof output"
                    fi
                done
            done <<< "$port_lines_lsof"
        fi

        # Method 4: Direct port number
        if [[ "$service" =~ ^[0-9]+$ ]]; then
            found=true
            if [[ ! " ${ports[*]} " =~ " $service " ]]; then
                ports+=("$service")
                log "Using direct port number: $service"
            fi
        fi

        if [[ "$found" = false ]]; then
            log "No ports found for service/process '$service'"
        fi
    done

    if [ ${#ports[@]} -gt 0 ]; then
        printf '%s\n' "${ports[@]}"
    fi
}

# ---------------------- TShark Process Management ----------------------
start_tshark_for_interface() {
    local interface=$1
    shift
    local services=("$@")

    local safe_interface=$(echo "$interface" | tr '/' '_')
    local output_file="/tmp/${IP}_${safe_interface}.pcapng"

    # Remove existing output file
    rm -f "$output_file" 2>/dev/null

    local filters=""
    local capture_desc=""

    # Build port filters if services specified
    if [ ${#services[@]} -gt 0 ]; then
        local port_list
        port_list=$(get_ports_by_services "${services[@]}")

        if [ -n "$port_list" ]; then
            local -a ports=()
            while IFS= read -r port; do
                if [[ "$port" =~ ^[0-9]+$ ]]; then
                    ports+=("$port")
                fi
            done <<< "$port_list"

            for port in "${ports[@]}"; do
                filters+=" or tcp port $port or udp port $port"
            done
            filters="${filters# or }"

            log "Interface $interface - Found ports: ${ports[*]}"
            capture_desc="for services: ${services[*]}"
        else
            log "Interface $interface - No ports found for specified services. Capturing all traffic"
            capture_desc="all traffic"
        fi
    else
        log "Interface $interface - No services specified. Capturing all traffic"
        capture_desc="all traffic"
    fi

    # Start tshark process
    if [ -n "$filters" ]; then
        log "Starting tshark on $interface with filters: $filters"
        nohup "$TSHARK_PATH" -i "$interface" -f "$filters" -w "$output_file" > /dev/null 2>&1 &
    else
        log "Starting tshark on $interface without filters"
        nohup "$TSHARK_PATH" -i "$interface" -w "$output_file" > /dev/null 2>&1 &
    fi

    local tshark_pid=$!
    log "Tshark process started with PID: $tshark_pid for interface $interface"

    # Wait for file creation and set permissions
    for i in {1..10}; do
        if [ -f "$output_file" ]; then
            chmod 777 "$output_file"
            log "Permissions set to 777 for $output_file"
            break
        fi
        sleep 1
    done

    sleep 1

    if ps -p $tshark_pid > /dev/null 2>&1; then
        log "tshark started successfully on interface $interface with PID: $tshark_pid"
        echo "tshark started successfully on interface: $interface"
        return 0
    else
        log "Failed to start tshark on interface $interface"
        echo "Failed to start tshark on interface: $interface"
        return 1
    fi
}

start_tshark() {
    local interfaces=()
    local services=()
    local current_section=""

    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            -i)
                current_section="interface"
                shift
                ;;
            -m)
                current_section="module"
                shift
                ;;
            *)
                if [[ "$current_section" == "interface" ]]; then
                    interfaces+=("$1")
                elif [[ "$current_section" == "module" ]]; then
                    services+=("$1")
                else
                    log "Unknown parameter without -i or -m: $1"
                fi
                shift
                ;;
        esac
    done

    # Set default interface if none specified
    if [ ${#interfaces[@]} -eq 0 ]; then
        interfaces=("any")
        log "No interface specified, using default: any"
    fi

    # Validate all interfaces
    for interface in "${interfaces[@]}"; do
        if ! validate_interface "$interface"; then
            log "Invalid interface: $interface"
            echo "ERROR: Invalid interface: $interface"
            return 1
        fi
    done

    log "Starting tshark on interfaces: ${interfaces[*]} with services: ${services[*]}"

    # Start tshark for each interface
    local success_count=0
    for interface in "${interfaces[@]}"; do
        if start_tshark_for_interface "$interface" "${services[@]}"; then
            ((success_count++))
        fi
        sleep 0.5
    done

    log "Started tshark on $success_count out of ${#interfaces[@]} interfaces"
    echo "Started tshark on $success_count out of ${#interfaces[@]} interfaces"
}

# ---------------------- Stop Functions ----------------------
stop_tshark_for_interface() {
    local interface=$1
    local pids=()

    # Find all tshark processes for this interface
    while IFS= read -r pid; do
        pids+=("$pid")
    done < <(pgrep -f "tshark.*-i $interface")

    if [ ${#pids[@]} -eq 0 ]; then
        log "No tshark processes found for interface: $interface"
        return 0
    fi

    for pid in "${pids[@]}"; do
        kill "$pid" 2>/dev/null
        log "Stopped tshark process with PID: $pid on interface $interface"
    done

    echo "Stopped tshark on interface: $interface"
    return 0
}

stop_tshark() {
    local interfaces=()
    local services=()
    local current_section=""

    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            -i)
                current_section="interface"
                shift
                ;;
            -m)
                current_section="module"
                shift
                ;;
            *)
                if [[ "$current_section" == "interface" ]]; then
                    interfaces+=("$1")
                elif [[ "$current_section" == "module" ]]; then
                    services+=("$1")
                else
                    log "Unknown parameter without -i or -m: $1"
                fi
                shift
                ;;
        esac
    done

    # Check if tshark is running
    if ! pgrep -x "$PROCESS_NAME" > /dev/null 2>&1; then
        log "tshark is not running."
        echo "tshark is not running."
        return 0
    fi

    # Stop all processes if no criteria specified
    if [ ${#interfaces[@]} -eq 0 ] && [ ${#services[@]} -eq 0 ]; then
        log "Stopping all tshark processes..."
        pkill -x "$PROCESS_NAME" 2>/dev/null
        log "All tshark processes stopped."
        echo "All tshark processes stopped."
        return 0
    fi

    # Stop by interfaces
    if [ ${#interfaces[@]} -gt 0 ]; then
        for interface in "${interfaces[@]}"; do
            stop_tshark_for_interface "$interface"
        done
        return 0
    fi

    # Stop by services
    if [ ${#services[@]} -gt 0 ]; then
        log "Stopping tshark processes for services: ${services[*]}"
        local port_list=$(get_ports_by_services "${services[@]}")
        local -a ports=()

        if [ -n "$port_list" ]; then
            while IFS= read -r port; do
                if [[ "$port" =~ ^[0-9]+$ ]]; then
                    ports+=("$port")
                fi
            done <<< "$port_list"
        fi

        local pids_to_kill=()
        while IFS= read -r pid; do
            local full_cmd=$(ps -p "$pid" -o args= 2>/dev/null)
            if [[ -n "$full_cmd" ]]; then
                local should_kill=false
                for port in "${ports[@]}"; do
                    if [[ "$full_cmd" == *"port $port"* ]]; then
                        should_kill=true
                        break
                    fi
                done
                if [ "$should_kill" = true ]; then
                    pids_to_kill+=("$pid")
                fi
            fi
        done < <(pgrep -x "$PROCESS_NAME")

        if [ ${#pids_to_kill[@]} -eq 0 ]; then
            log "No tshark processes found for specified services"
            echo "No tshark processes found for specified services"
        else
            for pid in "${pids_to_kill[@]}"; do
                kill "$pid"
                log "Stopped tshark process with PID: $pid"
            done
            echo "Stopped ${#pids_to_kill[@]} tshark process(es)"
        fi
    fi
}

# ---------------------- Status and Information ----------------------
status_tshark() {
    if pgrep -x "$PROCESS_NAME" > /dev/null 2>&1; then
        log "tshark is currently running."
        echo "tshark is currently running:"
        pgrep -x "$PROCESS_NAME" | while read pid; do
            local cmd=$(ps -p "$pid" -o args= 2>/dev/null)
            log "PID $pid: $cmd"
            echo "PID $pid: $cmd"
        done
    else
        log "tshark is not running."
        echo "tshark is not running."
    fi
}

list_services() {
    log "Available services and processes with open ports:"
    echo "Available services and processes with open ports:"
    ss -tulnp 2>/dev/null | grep -v "pid=" | awk '{print $7}' | cut -d'"' -f2 | sort -u | while read process; do
        if [[ -n "$process" && "$process" != "-" ]]; then
            local ports=$(ss -tulnp 2>/dev/null | grep "\"$process\"" | awk '{print $5}' | awk -F':' '{print $NF}' | sort -u | tr '\n' ' ')
            log "$process - ports: $ports"
            echo "$process - ports: $ports"
        fi
    done
}

show_interfaces() {
    local interface_list
    interface_list=$(get_available_interfaces)

    if [ -z "$interface_list" ]; then
        echo "ERROR: Could not get interface list from tshark"
        return 1
    fi

    echo "Available interfaces for capture:"
    echo "----------------------------------------"
    echo "$interface_list"
}

# ---------------------- Main Program ----------------------
case "$1" in
    start)
        shift
        start_tshark "$@"
        ;;
    stop)
        shift
        stop_tshark "$@"
        ;;
    status)
        status_tshark
        ;;
    list)
        list_services
        ;;
    interfaces)
        show_interfaces
        ;;
    *)
        echo "Usage: $0 {start|stop|status|list|interfaces}"
        echo ""
        echo "Examples:"
        echo "  $0 start -i any -m apache2 nginx"
        echo "  $0 start -i eth0 -i wlo1 -m 80 443"
        echo "  $0 start -i any"
        echo "  $0 stop -i eth0 -i wlo1"
        echo "  $0 stop -m apache2"
        echo "  $0 stop"
        echo "  $0 status"
        echo "  $0 list"
        echo "  $0 interfaces"
        exit 1
        ;;
esac
