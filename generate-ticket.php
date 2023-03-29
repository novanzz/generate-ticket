<?php
require "Database.php";

class GenerateTicket
{
    private $conn;
    private $event_id;
    private $total_ticket;

    public function __construct($event_id, $total_ticket)
    {
        $database = new Database();
        $db = $database->connect();
        $this->conn = $db;
        $this->event_id = $event_id;
        $this->total_ticket = $total_ticket;
    }

    function genTicket()
    {
        $prefix = $this->generatePrefix();
        $length_random = 7;
        $code_ticket = [];
        for ($i = 0; $i <  $this->total_ticket; $i++) {
            array_push($code_ticket, $this->generateRandom($length_random, $prefix));
        }

        $this->insertCode($code_ticket);
    }

    function generatePrefix()
    {
        $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $length_prefix = 3;
        $prefix = "";

        /*  
            inisialisasi seed berdasarkan ID
            agar setiap id yang sama dan di input kembali memiliki hasil prefix yang sama
        */
        srand($this->event_id);

        for ($i = 0; $i < $length_prefix; $i++) {
            $prefix .= $alphabet[rand(0, strlen($alphabet) - 1)];
        }
        return $prefix;
    }

    function generateRandom($length_random, $prefix)
    {
        // generate random alphanumerik di dapat dari md5 dengan param uniqid
        $suffix = strtoupper(substr(md5(uniqid()), 0, $length_random));
        return $prefix . $suffix;
    }

    function insertCode($code_ticket)
    {
        $this->conn;
        $table = "ticket";
        /*
            chunk jumlah code tiket dilakukan agar dalam proses query insert multiple row
            proses nya tidak terlalu besar, jika data yang di masukkan lebih dari 1juta row dalam sekali query
            maka proset insert akan sangat berat jika tidak di chunk
        */
        $chunk_code_ticket = array_chunk($code_ticket, 500);
        foreach ($chunk_code_ticket as $code_ticket) {
            $sql = "INSERT INTO " . $table . " (ticket_code) VALUES ";
            $insertQuery = [];
            $insertData = [];
            $n = 0;
            foreach ($code_ticket as $code) {
                ++$n;
                $insertQuery[] = '(:ticket_code' . $n . ')';
                $insertData[":ticket_code" . $n] = $code;
            }

            if (empty($insertQuery)) {
                echo "Gagal insert kode tiket ke database \n";
                exit();
            }

            $sql .= implode(', ', $insertQuery);
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($insertData);
            $row = $stmt->rowCount();
            if ($row < 1) {
                echo "Gagal insert kode tiket ke database \n";
                exit();
            }
            echo "Berhasil insert kode tiket ke database \n";
        }
    }
}

$genTicket = new GenerateTicket((int)$argv[1], (int)$argv[2]);
$genTicket->genTicket();
