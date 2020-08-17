<?php

class TcpClient
{
    private $socket = null;
    private $address = null;
    private $port = null;

    public function connect4($ip, $port)
    {
        $this->createSocket4();
        $this->isClient = true;
        $this->address = $ip;
        $this->port = $port;
        $this->connect();
    }

    public function connect6($ip, $port)
    {
        $this->createSocket6();
        $this->isClient = true;
        $this->address = $ip;
        $this->port = $port;
        $this->connect();
    }

    private function connect()
    {
        $socket = $this->socket;
        $address = $this->address;
        $port = $this->port;
        $conn = socket_connect($socket, $address, $port);
        if (!$conn) {
            $err_msg = socket_strerror(socket_last_error($socket));
            socket_close($socket);
            die('cannot connect '.$address.':'.$port);
        }
    }

    private function createSocket4()
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (!$socket) {
            $err_msg = socket_strerror(socket_last_error($socket));
            socket_close($socket);
            throw new Exception('socket_create() failed:'.$err_msg);
        } else {
            socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array(
                'sec' => 5,
                'usec' => 0,
            ));
            if (!$socket) {
                $err_msg = socket_strerror(socket_last_error($socket));
                socket_close($socket);
                throw new Exception('socket_create() failed:'.$err_msg);
            }
            $this->socket = $socket;
        }
    }

    private function createSocket6()
    {
        $socket = socket_create(AF_INET6, SOCK_STREAM, SOL_TCP);

        if (!$socket) {
            $err_msg = socket_strerror(socket_last_error($socket));
            socket_close($socket);
            throw new Exception('socket_create() failed:'.$err_msg);
        } else {
            socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array(
                'sec' => 5,
                'usec' => 0,
            ));
            if (!$socket) {
                $err_msg = socket_strerror(socket_last_error($socket));
                socket_close($socket);
                throw new Exception('socket_create() failed:'.$err_msg);
            }
            $this->socket = $socket;
        }
    }

    public function write(&$data, $length)
    {
        if (!$this->isClient) {
            return 0;
        }
        $socket = $this->socket;
        $command = pack('C'.$length, ...$data);
        $res = socket_write($socket, $command, $length);
        if (!$res) {
            $err_msg = socket_strerror(socket_last_error($socket));
            socket_close($socket);
            throw 'unable send data';
        }

        return $res;
    }

    public function read(&$data, $length)
    {
        $socket = $this->socket;
        if (false === ($bytes = socket_recv($socket, $data, $length, MSG_WAITALL))) {
            $err_msg = socket_strerror(socket_last_error($socket));
            socket_close($socket);
            throw 'unable receive data';
        }

        return $bytes;
    }

    public function close()
    {
        if (is_resource($this->socket)) {
            socket_shutdown($this->socket);
            socket_close($this->socket);
        }
    }
}
