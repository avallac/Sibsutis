<?php

namespace Http;

class Server
{

    private function postReply($client, $output)
    {
        $head = 'HTTP/1.1 200 OK' . "\r\n" . 'Content-Type: text/html' . "\r\n\r\n";
        socket_write($client, $head . $output);
    }

    public function run($address, $port, $VM)
    {
        $params = array();
        set_time_limit(0);
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_bind($sock, $address, $port) or die('Could not bind to address');
        while (1) {
            socket_listen($sock);
            if ($client = socket_accept($sock)) {
                $input = socket_read($client, 4096);
                $pattern1 = '/^(GET|POST) (\/[^ ]+) HTTP/';
                if (preg_match($pattern1, $input, $matches)) {
                    if ($matches[1] == 'POST') {
                        $strings = explode("\n", $input);
                        parse_str(end($strings), $params);
                    }
                    if ($matches[2] !== '/favicon.ico') {
                        $main = new MainController();
                        $function = substr($matches[2], 1);
                        if (method_exists($main, $function)) {
                            $this->postReply($client, $main->$function($VM, $params));
                        } else {
                            $this->postReply($client, $main->e404());
                        }
                    }
                } else {
                    $main = new MainController();
                    $this->postReply($client, $main->index($VM));
                }
                socket_close($client);
            }
        }
        socket_close($sock);
    }
}
