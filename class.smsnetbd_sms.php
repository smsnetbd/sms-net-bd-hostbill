<?php
class smsnetbd_sms extends NotificationModule
{
    protected $version = "1.2025-01-01";
    protected $modname = "sms.net.bd SMS Notifications";
    protected $description = "Send SMS notifications via sms.net.bd gateway for staff and clients.";

    protected $configuration = [
        "API Key" => [
            "value" => "",
            "type" => "input",
            "description" => "Your API Key from sms.net.bd panel"
        ],
        "Sender ID" => [
            "value" => "",
            "type" => "input",
            "description" => "Approved Sender ID (optional)"
        ]
    ];

    public function install()
    {
       // No Need 
    }

    public function notify($address, $message, array $details)
    {
        if(!$address) return false;
        return $this->_send($address, $message);
    }

    public function notifyAdmin($admin_id, $subject, $message)
    {
        $editadmins = HBLoader::LoadModel("EditAdmins");
        $admin = $editadmins->getAdminDetails($admin_id);
        if(!$admin || empty($admin["mobilephone"])) return false;
        return $this->_send($admin["mobilephone"], $message);
    }

    public function notifyClient($client_id, $subject, $message)
    {
        $clients = HBLoader::LoadModel("Clients");
        $client = $clients->getClient($client_id);
        if(!$client || empty($client["phonenumber"])) return false;
        return $this->_send($client["phonenumber"], $message);
    }

    public function sendClientSMS($to, $from, $text)
    {
        $this->configuration["Sender ID"]["value"] = $from;
        return $this->_send($to, $text);
    }

    private function _send($number, $message)
    {
        $number = preg_replace("/[^0-9]/", "", $number);
        $api_key = trim($this->configuration["API Key"]["value"]);
        $sender  = trim($this->configuration["Sender ID"]["value"]);

        if(!$api_key) {
            $this->addError("SMSNETBD: API Key not configured.");
            return false;
        }

        $params = [
            "api_key" => $api_key,
            "msg"     => $message,
            "to"      => $number
        ];
        if($sender) $params["sender_id"] = $sender;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sms.net.bd/sendsms");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $ret = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if(!$ret) {
            $this->addError("SMSNETBD CURL Error: " . $err);
            hbm_log_system("SMSNETBD CURL Error: " . $err, "smsnetbd");
            return false;
        }

        $resp = json_decode($ret, true);
        if(isset($resp["error"]) && $resp["error"] == 0) {
            hbm_log_system("SMS sent successfully to {$number}.", "smsnetbd");
            return true;
        } else {
            $errorMsg = $resp["msg"] ?? "Unknown error";
            $this->addError("SMSNETBD API Error: " . $errorMsg);
            hbm_log_system("SMSNETBD API Error: " . $errorMsg, "smsnetbd");
            return false;
        }
    }
}
?>
