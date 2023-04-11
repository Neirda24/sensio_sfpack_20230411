<?php

namespace App\Tests\Controller;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Controller\HelloController
 */
class HelloControllerTest extends WebTestCase
{
    public static function provideValidUriWithStatusCodes(): Generator
    {
        yield 'default' => [
            'uri' => '/hello',
            'expectedStatusCode' => 200,
        ];

        yield 'name "Adrien"' => [
            'uri' => '/hello/Adrien',
            'expectedStatusCode' => 200,
        ];

        yield 'name "Jean-Paul"' => [
            'uri' => '/hello/Jean-Paul',
            'expectedStatusCode' => 200,
        ];
    }

    /**
     * @dataProvider provideValidUriWithStatusCodes
     *
     * @group smoke-test
     */
    public function testThePageRespondsCorrectly(string $uri, int $expectedStatusCode): void
    {
        $client = static::createClient();
        $client->request('GET', $uri);

        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }
}
