<?php
class Service {
    private $db;
    private $proxmox;

    public function __construct() {
        require_once '../app/lib/Database.php';
        require_once '../app/lib/ProxmoxAPI.php';
        $this->db = new Database();
        
        try {
            $this->proxmox = new ProxmoxAPI();
        } catch (Exception $e) {
            error_log("Proxmox API initialization failed: " . $e->getMessage());
            $this->proxmox = null;
        }
    }

    public function createService($userId, $type, $config) {
        try {
            // Получаем следующий свободный ID для VM
            $vmid = $this->proxmox->getNextVMID();
            
            // Создаем виртуальную машину в Proxmox
            if ($type === 'vps') {
                $result = $this->proxmox->createVM($vmid, $config);
            } elseif ($type === 'lxc') {
                $result = $this->proxmox->createLXC($vmid, $config);
            } else {
                throw new Exception("Unknown service type: $type");
            }

            if (isset($result['data'])) {
                // Сохраняем в базу данных
                $this->db->query(
                    "INSERT INTO services (user_id, type, status, config) VALUES (?, ?, 'pending', ?)",
                    [$userId, $type, json_encode($config)]
                );
                
                $serviceId = $this->db->getLastInsertId();
                
                // Запускаем VM
                $this->proxmox->startVM($vmid);
                
                // Обновляем статус
                $this->db->query(
                    "UPDATE services SET status = 'active', config = ? WHERE id = ?",
                    [json_encode(array_merge($config, ['vmid' => $vmid])), $serviceId]
                );

                return $serviceId;
            }
            
            throw new Exception("Failed to create service in Proxmox");
            
        } catch (Exception $e) {
            error_log("Service creation error: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUserServices($userId) {
        return $this->db->query(
            "SELECT * FROM services WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        )->fetchAll();
    }

    public function getServiceStatus($serviceId) {
        $service = $this->db->query(
            "SELECT * FROM services WHERE id = ?",
            [$serviceId]
        )->fetch();

        if ($service && $this->proxmox) {
            try {
                $config = json_decode($service['config'], true);
                $vmid = $config['vmid'] ?? null;
                
                if ($vmid) {
                    $status = $this->proxmox->getVMStatus($vmid);
                    return $status['data'] ?? ['status' => 'unknown'];
                }
            } catch (Exception $e) {
                error_log("Status check error: " . $e->getMessage());
            }
        }

        return ['status' => 'unknown'];
    }

    public function suspendService($serviceId) {
        $service = $this->db->query(
            "SELECT * FROM services WHERE id = ?",
            [$serviceId]
        )->fetch();

        if ($service && $this->proxmox) {
            try {
                $config = json_decode($service['config'], true);
                $vmid = $config['vmid'] ?? null;
                
                if ($vmid) {
                    $this->proxmox->stopVM($vmid);
                    $this->db->query(
                        "UPDATE services SET status = 'suspended' WHERE id = ?",
                        [$serviceId]
                    );
                    return true;
                }
            } catch (Exception $e) {
                error_log("Suspend error: " . $e->getMessage());
            }
        }

        return false;
    }
}
?>