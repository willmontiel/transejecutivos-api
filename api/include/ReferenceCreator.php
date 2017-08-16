<?php

require_once 'LoggerHandler.php';

class ReferenceCreator {
    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    public function getReference() {
        $lref = $this->getLastReference();
        $num = preg_replace('/\D/', '', $lref);
        $num = substr($num, 0, -1);
        $num = $num + 10;
        return "U{$num}-1";
    }

    private function getLastReference() {
        $stmt = $this->conn->prepare("SELECT referencia FROM orden ORDER BY id DESC LIMIT 1");

        if ($stmt->execute()) {
            $stmt->bind_result($ref);
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->fetch();
                $stmt->close();

                return $ref;
            }
        }

        return "";
    }
}