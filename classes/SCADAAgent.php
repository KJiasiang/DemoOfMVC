<?php

class SCADAAgent
{
    public function __construct()
    {
        ModbusTCP::open('::');
    }

    public function getSystemInfo()
    {
        $result = array();
        $data = ModbusTCP::ReadInputRegisters(65000, 45);

        $result['com'] = $data[40];
        $result['interface'] = $data[41];

        $keys = array('sn' => 0, 'mac' => 10, 'index' => 20, 'type' => 30);
        foreach ($keys as $key => $value) {
            $result[$key] = $this->wordToString($data, $value, 10);
        }

        $data = ModbusTCP::ReadInputRegisters(63450, 10);
        $result['interfaceExtra1'] = $data[0];
        $result['interfaceExtra2'] = $data[1];
        $result['interfaceExtra3'] = $data[2];
        $result['interfaceExtra4'] = $data[3];

        return $result;
    }

    private function wordToString(&$data, $offset, $count)
    {
        $result = '';
        for ($i = 0; $i < 10; ++$i) {
            $result .= chr(($data[$i + $offset] & 0xff00) >> 8);
            $result .= chr($data[$i + $offset] & 0xff);
        }

        return trim($result);
    }

    private function LoadIpData($index)
    {
        $addr = "-------";
        if (file_exists("/home/moxa/ip$index")) {
            $f = file("/home/moxa/ip$index");
            if (count($f) > 0)
                $addr = $f[0];
        }

        return $addr;
    }

    public function getNetInterface()
    {
        $result = array();
        $info = $this->getSystemInfo();

        $data = ModbusTCP::ReadInputRegisters(63400, 50);

        if ($data[0] == 999) {
            $addr = $this->LoadIpData(0);
            array_push($result, array(
                'address' => $addr, 'netmask' => '0.0.0.0',
                'gateway' => '0.0.0.0', 'eth' => 0, 'extra' => -1, 'extra' => array(), 'type' => 1,
            ));
        } else
            array_push($result, array(
                'address' => $this->getIPString($data, 0), 'netmask' => $this->getIPString($data, 4),
                'gateway' => $this->getIPString($data, 8), 'eth' => 0, 'extra' => -1, 'extra' => array(), 'type' => 0,
            ));

        for ($i = 0; $i < $info['interface'] - 1; ++$i) {
            if ($data[20 + $i * 10] == 999) {
                $addr = $this->LoadIpData($i + 1);
                array_push(
                    $result,
                    array(
                        'address' => $addr,
                        'netmask' => '0.0.0.0', 'eth' => $i + 1, 'extra' => -1, 'extra' => array(), 'type' => 1,
                    )
                );
            } else
                array_push(
                    $result,
                    array(
                        'address' => $this->getIPString($data, 20 + $i * 10),
                        'netmask' => $this->getIPString($data, 24 + $i * 10), 'eth' => $i + 1, 'extra' => -1, 'extra' => array(), 'type' => 0,
                    )
                );
        }

        for ($j = 1; $j <= 4; ++$j) {
            if ($info['interfaceExtra' . $j] <= 0) {
                continue;
            }
            $data1 = ModbusTCP::ReadInputRegisters(64100 + ($j - 1) * 200, 100);
            $data2 = ModbusTCP::ReadInputRegisters(64200 + ($j - 1) * 200, 100);
            for ($i = 0; $i < $info['interfaceExtra' . $j]; ++$i) {
                array_push(
                    $result[$j - 1]['extra'],
                    array(
                        'address' => $this->getIPString($data1, $i * 4),
                        'netmask' => $this->getIPString($data2, $i * 4), 'eth' => $j - 1, 'extra' => $i,
                    )
                );
            }
        }

        return $result;
    }

    public function setNetInterface($data)
    {
        $size = count($data);
        $values = array();

        for ($i = 0; $i < $size; ++$i) {
            if ($data[$i]->type == 1)
                $a = $this->getIPArray($values, '999.999.999.999');
            else
                $a = $this->getIPArray($values, $data[$i]->address);
            $a = $this->getIPArray($values, $data[$i]->netmask);
            if (0 == $i) {
                $a = $this->getIPArray($values, $data[$i]->gateway);
                for ($j = 0; $j < 8; ++$j) {
                    array_push($values, 0);
                }
            }
        }
        ModbusTCP::PresetMultipleRegisters(63400, $values);

        for ($i = 0; $i < $size; ++$i) {
            $count = count($data[$i]->extra);
            ModbusTCP::PresetSingleRegister(63450 + $i, $count);
            $valuesA = array();
            $valuesB = array();
            for ($j = 0; $j < $count; ++$j) {
                $a = $this->getIPArray($valuesA, $data[$i]->extra[$j]->address);
                $b = $this->getIPArray($valuesB, $data[$i]->extra[$j]->netmask);
            }
            ModbusTCP::PresetMultipleRegisters(64100 + $i * 200, $valuesA);
            ModbusTCP::PresetMultipleRegisters(64200 + $i * 200, $valuesB);
        }
    }

    public function getCOM($index)
    {
        $result = array();
        $data = ModbusTCP::ReadInputRegisters(60000 + $index * 200, 12);

        $keys = array('mode' => 0, 'databits' => 3, 'parity' => 4, 'stopbits' => 5, 'flow' => 6, 'timeout' => 7, 'retry' => 8, 'delay' => 9, 'format' => 10, 'count' => 11);
        foreach ($keys as $key => $value) {
            $result[$key] = $data[$value];
        }

        $result['baud'] = $data[1] * 0x10000 + $data[2];

        $result['formatString'] = $this->getFormatString($result['format']);
        $result['parameter'] = array();
        $data = ModbusTCP::ReadInputRegisters(60012 + $index * 200, $result['count']);
        for ($i = 0; $i < $result['count']; ++$i) {
            array_push($result['parameter'], $data[$i]);
        }

        return $result;
    }

    public function getNetwork($index)
    {
        $result = array();
        $data = ModbusTCP::ReadInputRegisters(50000 + $index * 200, 12);

        $keys = array('port' => 5, 'timeout' => 6, 'retry' => 7, 'delay' => 8, 'format' => 0, 'count' => 9);
        foreach ($keys as $key => $value) {
            $result[$key] = $data[$value];
        }

        $result['ip'] = $this->getIPString($data, 1);
        $result['formatString'] = $this->getFormatString($result['format']);
        $result['parameter'] = array();
        $data = ModbusTCP::ReadInputRegisters(50010 + $index * 200, $result['count']);
        for ($i = 0; $i < $result['count']; ++$i) {
            array_push($result['parameter'], $data[$i]);
        }

        return $result;
    }

    private function getIPString(&$data, $offset)
    {
        $result = '';
        for ($i = 0; $i < 4; ++$i) {
            $result .= $data[$i + $offset];
            if ($i < 3) {
                $result .= '.';
            }
        }

        return $result;
    }

    private function getIPArray(&$values, $ip)
    {
        $r = explode('.', $ip);
        for ($i = 0; $i < 4; ++$i) {
            array_push($values, (int) $r[$i]);
        }

        return $r;
    }

    public function getFormatString($format)
    {
        switch ($format) {
            case 1:
                return 'Edwrads';
            case 2:
                return 'Ebara';
            case 3:
                return 'Kashiyama';
            case 4:
                return 'Modbus';
            case 5:
                return 'ModbusE54';
            case 6:
                return 'Manual';
            case 7:
                return "FinsTCP";
            case 999:
                return "Auto";
            case 0:
                $r = array();
                for ($i = 1; $i < 8; $i++)
                    array_push($r, array('key' => $i, 'value' => $this->getFormatString($i)));
                array_push($r, array('key' => 999, 'value' => $this->getFormatString(999)));
                return $r;
        }

        return 'None';
    }

    public function getCOMConnectionStatus()
    {
        return ModbusTCP::ReadInputStatus(50100, 16);
    }

    public function getNetworkConnectionStatus()
    {
        return ModbusTCP::ReadInputStatus(50000, 50);
    }

    public function getCOMWorkStatus()
    {
        return ModbusTCP::ReadInputStatus(50300, 16);
    }

    public function getNetworkWorkStatus()
    {
        return ModbusTCP::ReadInputStatus(50200, 50);
    }

    public function getVersionInfo()
    {
        $result = array();
        $data = ModbusTCP::ReadInputRegisters(64000, 30);

        $keys = array('watchdog' => 0, 'filetransfer' => 3, 'runner' => 6, 'modbus' => 9, 'hsms' => 12, 'catcher' => 15, 'verify' => 18, 'setting' => 21);

        foreach ($keys as $key => $value) {
            $result[$key] = $this->getVersionString($data, $value);
        }

        return $result;
    }

    private function getVersionString(&$data, $offset)
    {
        return $data[$offset] . '.' . $data[$offset + 1] . '.' . $data[$offset + 2];
    }

    public function setCOM($data, $index)
    {
        $serial = array();
        array_push($serial, $data['mode']);
        array_push($serial, ($data['baud'] & 0xffff0000) >> 16);
        array_push($serial, $data['baud'] & 0xffff);
        array_push($serial, $data['databits']);
        array_push($serial, $data['parity']);
        array_push($serial, $data['stopbits']);
        array_push($serial, $data['flow']);
        array_push($serial, $data['timeout']);
        array_push($serial, $data['retry']);
        array_push($serial, $data['delay']);
        array_push($serial, $data['format']);
        array_push($serial, $data['count']);

        $address = 60000 + $index * 200;

        ModbusTCP::PresetMultipleRegisters($address, $serial);
        if ($data['count'] > 0) {
            ModbusTCP::PresetMultipleRegisters($address + 12, $data['parameter']);
        }
    }

    public function setNetwork($data, $index)
    {
        $net = array();
        array_push($net, (int) $data['format']);

        $ips = explode('.', $data['ip']);
        for ($i = 0; $i < 4; ++$i) {
            array_push($net, (int) $ips[$i]);
        }

        array_push($net, $data['port']);
        array_push($net, $data['timeout']);
        array_push($net, $data['retry']);
        array_push($net, $data['delay']);

        array_push($net, $data['count']);

        $address = 50000 + $index * 200;

        ModbusTCP::PresetMultipleRegisters($address, $net);
        if ($data['count'] > 0) {
            ModbusTCP::PresetMultipleRegisters($address + 10, $data['parameter']);
        }
    }

    public function resetCOM($index)
    {
        return $this->operation(201 + $index);
    }

    public function resetAllCOM()
    {
        return $this->operation(217);
    }

    public function resetNetwork($index)
    {
        return $this->operation(101 + $index);
    }

    public function resetAllNetwork()
    {
        return $this->operation(151);
    }

    public function resetLAN()
    {
        return $this->operation(3);
    }

    private function operation($code)
    {
        $addr = 65535;
        $count = 0;
        for (;;) {
            $data = ModbusTCP::ReadHoldingRegisters($addr, 1);

            if ($data[0]) {
                if (++$count >= 10) {
                    die('wait too long 1');
                } else {
                    usleep(100000);
                }
            } else {
                break;
            }
        }

        ModbusTCP::PresetSingleRegister($addr, $code);

        $flag = false;
        $count = 0;
        for (;;) {
            $data = ModbusTCP::ReadInputRegisters($addr, 1);
            if (8031 == $data[0]) {
                ModbusTCP::PresetSingleRegister($addr, 5426);
                break;
            } else {
                if (++$count > 10) {
                    die('wait too long 2');
                }
                usleep(100000);
            }
        }
        $count = 0;
        for (;;) {
            $data = ModbusTCP::ReadInputRegisters($addr, 1);
            if (8031 == $data[0] || 6666 == $data[0]) {
                if (++$count > 10) {
                    die('wait too long 3');
                }
                usleep(100000);
            } else {
                sleep(2);

                return $data[0];
            }
        }
    }

    public function rebootDevice()
    {
        return $this->operation(1);
    }

    public function shutdownDevice()
    {
        return $this->operation(2);
    }

    public function update()
    {
        return $this->operation(9999);
    }

    public function getNetworkCount($index)
    {
        $data = ModbusTCP::ReadInputRegisters(63520 + $index, 1);

        return $data[0];
    }

    public function getCOMCount($index)
    {
        $data = ModbusTCP::ReadInputRegisters(63500 + $index, 1);

        return $data[0];
    }

    public function getNetworkResetState($index)
    {
        $data = ModbusTCP::ReadInputStatus(50400 + $index, 1);

        return $data[0];
    }

    public function getCOMResetState($index)
    {
        $data = ModbusTCP::ReadInputStatus(50500 + $index, 1);

        return $data[0];
    }

    public function __destruct()
    {
        ModbusTCP::close();
    }
}
