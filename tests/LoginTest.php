<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testSuccessfulLogin()
    {
        $client = self::createClient();

        $crawler = $client->request('GET', 'login');

        $loginForm = $crawler
            ->selectButton('_submit')
            ->form(
                array(
                    '_username' => 'splashkits@gmail.com',
                    '_password' => '12345',
                )
            );

        $client->submit($loginForm);

        $crawler = $client->followRedirect();

        $this->assertEquals('Welcome Home, pdr!', $crawler->filter('#welcome'));
    }

    /**
     * @return string
     */
    protected static function getKernelClass()
    {
        return 'App\Kernel';
    }
}