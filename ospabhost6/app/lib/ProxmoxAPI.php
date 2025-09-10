<?php
class ProxmoxAPI {
    private $baseUrl;
    private $node;
    private $authHeaders;

    public function __construct() {
        $this->baseUrl = PROXMOX_HOST . '/api2/json';
        $this->node = PROXMOX_NODE;
        
        // Используем API Token (рекомендуется)
        if (defined('PROXMOX_TOKEN_ID') && defined('PROXMOX_TOKEN_SECRET')) {
            $this->authHeaders = [
                "Authorization: PVEAPIToken=" . PROXMOX_TOKEN_ID . "=" . PROXMOX_TOKEN_SECRET
            ];
        } 
        // Или логин/пароль (менее безопасно)
        elseif (defined('PROXMOX_USER') && defined('PROXMOX_PASSWORD')) {
            $this->authHeaders = $this->getCSRFToken();
        } else {
            throw new Exception('Proxmox credentials not configured');
        }
    }

    private function getCSRFToken() {
        $url = $this->baseUrl . '/access/ticket';
        $data = [
            'username' => PROXMOX_USER,
            'password' => PROXMOX_PASSWORD,
            'realm' => PROXMOX_REALM
        ];

        $response = $this->request('POST', $url, $data, false);
        $result = json_decode($response, true);
        
        if (isset($result['data']['CSRFPreventionToken'])) {
            return [
                "CSRFPreventionToken: " . $result['data']['CSRFPreventionToken'],
                "Cookie: PVEAuthCookie=" . $result['data']['ticket']
            ];
        }
        
        throw new Exception('Failed to get CSRF token');
    }

    public function request($method, $url, $data = [], $useAuth = true) {
        $ch = curl_init();
        $headers = ['Content-Type: application/x-www-form-urlencoded'];
        
        if ($useAuth) {
            $headers = array_merge($headers, $this->authHeaders);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30
        ]);

        if ($method === 'POST' && !empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("Proxmox API Error: " . $error);
            throw new Exception("Proxmox API request failed: " . $error);
        }

        if ($httpCode >= 400) {
            error_log("Proxmox API HTTP $httpCode: " . $response);
            throw new Exception("Proxmox API returned HTTP $httpCode");
        }

        return $response;
    }

    public function createVM($vmid, $params) {
        $url = $this->baseUrl . "/nodes/{$this->node}/qemu";
        
        $defaultParams = [
            'vmid' => $vmid,
            'name' => 'vm-' . $vmid,
            'memory' => 1024,
            'cores' => 1,
            'sockets' => 1,
            'net0' => 'virtio,bridge=vmbr0',
            'scsihw' => 'virtio-scsi-pci',
            'bootdisk' => 'scsi0',
            'ostype' => 'l26'
        ];

        $finalParams = array_merge($defaultParams, $params);
        
        $response = $this->request('POST', $url, $finalParams);
        return json_decode($response, true);
    }

    public function createLXC($vmid, $params) {
        $url = $this->baseUrl . "/nodes/{$this->node}/lxc";
        
        $defaultParams = [
            'vmid' => $vmid,
            'hostname' => 'ct-' . $vmid,
            'memory' => 512,
            'swap' => 512,
            'cores' => 1,
            'ostemplate' => 'local:vztmpl/ubuntu-22.04-standard_22.04-1_amd64.tar.zst',
            'rootfs' => 'local-lvm:8',
            'net0' => 'name=eth0,bridge=vmbr0,ip=dhcp'
        ];

        $finalParams = array_merge($defaultParams, $params);
        
        $response = $this->request('POST', $url, $finalParams);
        return json_decode($response, true);
    }

    public function startVM($vmid) {
        $url = $this->baseUrl . "/nodes/{$this->node}/qemu/$vmid/status/start";
        $response = $this->request('POST', $url);
        return json_decode($response, true);
    }

    public function stopVM($vmid) {
        $url = $this->baseUrl . "/nodes/{$this->node}/qemu/$vmid/status/stop";
        $response = $this->request('POST', $url);
        return json_decode($response, true);
    }

    public function getVMStatus($vmid) {
        $url = $this->baseUrl . "/nodes/{$this->node}/qemu/$vmid/status/current";
        $response = $this->request('GET', $url);
        return json_decode($response, true);
    }

    public function getNextVMID() {
        $url = $this->baseUrl . "/cluster/nextid";
        $response = $this->request('GET', $url);
        $result = json_decode($response, true);
        return $result['data'] ?? 100;
    }

    public function getNodeStatus() {
        $url = $this->baseUrl . "/nodes/{$this->node}/status";
        $response = $this->request('GET', $url);
        return json_decode($response, true);
    }
}
?>