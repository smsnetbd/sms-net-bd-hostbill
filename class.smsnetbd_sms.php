<?php
class SMSNETBD extends NotificationModule
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
        ],
        "Client Field" => [
            "value" => "mobilephone",
            "type" => "input",
            "description" => "Client field containing phone number"
        ]
    ];

    public function install()
    {
        $admin_field = ["name" => "Mobile phone number", "code" => "mobilephone", "type" => "input"];
        $fieldsmanager = HBLoader::LoadModel("EditAdmins/AdminFields");
        $fieldsmanager->addField($admin_field);
        hbm_log_system("Installed SMSNETBD module and added admin field", "smsnetbd");
    }

    public function notify($address, $message, array $details)
    {
        if(!$address) {
            hbm_log_system("notify(): Missing destination address", "smsnetbd");
            return false;
        }
        return $this->_send($address, $message);
    }

    public function notifyAdmin($admin_id, $subject, $message)
    {
        $editadmins = HBLoader::LoadModel("EditAdmins");
        $admin = $editadmins->getAdminDetails($admin_id);
        if(!$admin) {
            hbm_log_system("notifyAdmin(): Invalid admin ID {$admin_id}", "smsnetbd");
            return false;
        }
        if(empty($admin["mobilephone"])) {
            hbm_log_system("notifyAdmin(): No mobile phone set for admin ID {$admin_id}", "smsnetbd");
            return false;
        }
        return $this->_send($admin["mobilephone"], $message);
    }

    public function notifyClient($client_id, $subject, $message)
    {
        $field = $this->configuration["Client Field"]["value"];
        if(!$field) {
            hbm_log_system("notifyClient(): Client field not configured", "smsnetbd");
            return false;
        }
        $clients = HBLoader::LoadModel("Clients");
        $client = $clients->getClient($client_id);
        if(!$client) {
            hbm_log_system("notifyClient(): Invalid client ID {$client_id}", "smsnetbd");
            return false;
        }
        if(empty($client[$field])) {
            hbm_log_system("notifyClient(): No mobile number in client field '{$field}' for client ID {$client_id}", "smsnetbd");
            return false;
        }
        return $this->_send($client[$field], $message);
    }

    public function sendClientSMS($to, $from, $text)
    {
        $this->configuration["Sender ID"]["value"] = $from;
        hbm_log_system("sendClientSMS(): Sending SMS to {$to} with custom sender {$from}", "smsnetbd");
        return $this->_send($to, $text);
    }

    private function _send($number, $message)
    {
        $number = preg_replace("/[^0-9]/", "", $number);
        $api_key = trim($this->configuration["API Key"]["value"]);
        $sender  = trim($this->configuration["Sender ID"]["value"]);

        if(!$api_key) {
            hbm_log_system("_send(): API Key not configured", "smsnetbd");
            return false;
        }

        $params = [
            "api_key" => $api_key,
            "msg"     => $message,
            "to"      => $number
        ];
        if($sender) $params["sender_id"] = $sender;

        hbm_log_system("_send(): Sending SMS to {$number} with message: {$message}", "smsnetbd");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sms.net.bd/sendsms");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $ret = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if(!$ret) {
            $this->addError("SMSNETBD error: " . $err);
            hbm_log_system("_send(): CURL Error - " . $err, "smsnetbd");
            return false;
        }

        $resp = json_decode($ret, true);
        if(isset($resp["error"]) && $resp["error"] == 0) {
            hbm_log_system("_send(): SMS sent successfully to {$number}", "smsnetbd");
            return true;
        } else {
            $errorMsg = $resp["msg"] ?? "Unknown error";
            $this->addError("SMSNETBD API Error: " . $errorMsg);
            hbm_log_system("_send(): API Error - {$errorMsg}", "smsnetbd");
            return false;
        }
    }
}
?>
