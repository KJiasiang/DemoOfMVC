<?php

class UdpClient
{
    private $socket = null;
    private $address = null;
    private $port = null;
    private $isClient = false;

    public function listen4($port)
    {
        $this->createSocket4();
        if (!socket_bind($this->socket, '0.0.0.0', $port)) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            die("code:$errorcode, msg:$errormsg");
        }
    }

    public function listen6($port)
    {
        $this->createSocket6();
        if (!socket_bind($this->socket, '::', $port)) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            die("code:$errorcode, msg:$errormsg");
        }
    }

    public function connect4($ip, $port)
    {
        $this->createSocket4();
        $this->isClient = true;
        $this->address = $ip;
        $this->port = $port;
    }

    public function connect6($ip, $port)
    {
        $this->createSocket6();
        $this->isClient = true;
        $this->address = $ip;
        $this->port = $port;
    }

    private function createSocket4()
    {
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        $this->setReceiveTimeout(5);
    }

    private function createSocket6()
    {
        $this->socket = socket_create(AF_INET6, SOCK_DGRAM, SOL_UDP);
        $this->setReceiveTimeout(5);
    }

    public function setReceiveTimeout($timeout)
    {
        $socket = $this->socket;
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array(
            'sec' => $timeout,
            'usec' => 0,
        ));
    }

    public function write(&$data, $length)
    {
        if (!$this->isClient) {
            return 0;
        }

        return $this->writeTo($data, $length, $this->address, $this->port);
    }

    public function writeTo(&$data, $length, $address, $port)
    {
        $socket = $this->socket;
        $command = pack('C'.$length, ...$data);
        $res = socket_sendto($socket, $command, $length, 0, $address, $port);

        return $res;
    }

    public function read(&$data, $length)
    {
        $socket = $this->socket;
        $bytes = socket_recvfrom($socket, $data, $length, 0, $ip, $port);
     
        return $bytes;
    }

    public function readToArray(&$data, $length)
    {
        $socket = $this->socket;
        $bytes = socket_recvfrom($socket, $data, $length, 0, $ip, $port);

        if ($bytes > 0) {
            $r = unpack('C'.$bytes, $data);
            $data = array();
            foreach ($r as $b) {
                array_push($data, $b);
            }
        }

        return $bytes;
    }

    public function close()
    {
        if (is_resource($this->socket)) {
            // socket_shutdown($this->socket);
            socket_close($this->socket);
        }
    }
}
