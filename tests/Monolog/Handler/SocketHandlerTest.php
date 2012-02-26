<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Handler\SocketHandler\MockSocket;
use Monolog\Handler\SocketHandler\Socket;
use Monolog\Handler\SocketHandler\PersistentSocket;

use Monolog\TestCase;
use Monolog\Logger;



/**
 * @author Pablo de Leon Belloc <pablolb@gmail.com>
 */
class SocketHandlerTest extends TestCase
{
    public function testWrite()
    {
        $socket = new MockSocket('localhost');
        $handler = new SocketHandler('localhost');
        $handler->setSocket($socket);
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord(Logger::WARNING, 'test'));
        $handler->handle($this->getRecord(Logger::WARNING, 'test2'));
        $handler->handle($this->getRecord(Logger::WARNING, 'test3'));
        $handle = $socket->getResource();
        fseek($handle, 0);
        $this->assertEquals('testtest2test3', fread($handle, 100));
    }
    
    public function testCloseClosesNonPersistentSocket()
    {
        $socket = new Socket('localhost');
        $res = fopen('php://memory', 'a');
        $socket->setResource($res);
        $handler = new SocketHandler('localhost');
        $handler->setSocket($socket);
        $handler->close();
        $this->assertFalse($socket->isConnected());
    }
    
    public function testCloseDoesNotClosePersistentSocket()
    {
        $socket = new PersistentSocket('localhost');
        $res = fopen('php://memory', 'a');
        $socket->setResource($res);
        $handler = new SocketHandler('localhost');
        $handler->setSocket($socket);
        $handler->close();
        $this->assertTrue($socket->isConnected());
    }
    
}
