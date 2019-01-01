<?php

namespace AlibabaCloud\Client\Tests\Unit;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\DefaultAcsClient;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Client\Profile\DefaultProfile;
use AlibabaCloud\Client\Result\Result;
use AlibabaCloud\Client\Tests\Mock\Services\Ecs\DescribeRegionsRequest;
use PHPUnit\Framework\TestCase;

/**
 * Class DefaultAcsClientTest
 *
 * @package   AlibabaCloud\Client\Tests\Unit\Client
 *
 * @author    Alibaba Cloud SDK <sdk-team@alibabacloud.com>
 * @copyright 2019 Alibaba Group
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @link      https://github.com/aliyun/openapi-sdk-php-client
 *
 * @coversDefaultClass \AlibabaCloud\Client\DefaultAcsClient
 */
class DefaultAcsClientTest extends TestCase
{

    /**
     * @var DefaultProfile
     */
    private static $profile;

    /**
     * @var DefaultAcsClient
     */
    private static $client;

    public function setUp()
    {
        parent::setUp();
        self::$profile = DefaultProfile::getProfile(
            'cn-hangzhou',
            \getenv('ACCESS_KEY_ID'),
            \getenv('ACCESS_KEY_SECRET')
        );
        self::$client  = new DefaultAcsClient(self::$profile);
    }

    public function testAccessKeyClient()
    {
        $request = new DescribeRegionsRequest();
        $request->setContent(\time());
        $this->assertEquals(
            \time(),
            $request->getContent()
        );

        $request = new DescribeRegionsRequest();
        $request->addHeader('time', \time());
        $this->assertArrayHasKey('time', $request->getHeaders());

        try {
            $request  = new DescribeRegionsRequest();
            $response = self::$client->getAcsResponse($request);
            $this->assertNotNull($response);
        } catch (ClientException $e) {
            $this->assertInstanceOf(
                ClientException::class,
                $e
            );
        } catch (ServerException $e) {
            self::assertContains(
                $e->getErrorMessage(),
                [
                    'Specified access key is not found.',
                ]
            );
        }
    }

    public function testJson()
    {
        try {
            $request = new DescribeRegionsRequest();
            $request->format('JSON');
            $response = self::$client->getAcsResponse($request);
            $this->assertNotNull($response);
        } catch (ClientException $e) {
            self::assertStringStartsWith('cURL error ', $e->getMessage());
        } catch (ServerException $e) {
            self::assertContains(
                $e->getErrorMessage(),
                [
                    'Specified access key is not found.',
                ]
            );
        }
    }

    public function testRaw()
    {
        try {
            $request = new DescribeRegionsRequest();
            $request->format('Raw');
            $result = self::$client->getAcsResponse($request);
            $this->assertInternalType('object', $result);
        } catch (ClientException $e) {
            self::assertStringStartsWith('cURL error ', $e->getMessage());
        } catch (ServerException $e) {
            self::assertEquals('', $e->getResult()->getResponse()->getBody()->getContents());
        }
    }

    public function testFormatNull()
    {
        try {
            $request = new DescribeRegionsRequest();
            $request->format(null);
            $response = self::$client->getAcsResponse($request);
            $this->assertNotNull($response);
        } catch (ClientException $e) {
            self::assertStringStartsWith('cURL error ', $e->getMessage());
        } catch (ServerException $e) {
            self::assertEquals('', $e->getResult()->getResponse()->getBody()->getContents());
        }
    }

    public function testBadMethod()
    {
        try {
            $request = new DescribeRegionsRequest();
            $request->method('BadMethod');
            $result = self::$client->getAcsResponse($request);
            self::assertEquals('', (string)$result);
        } catch (ClientException $e) {
            self::assertStringStartsWith('cURL error ', $e->getMessage());
        } catch (ServerException $e) {
            $expected = [
                'Specified access key is not found.',
            ];
            $this->assertContains($e->getErrorMessage(), $expected);
        }
    }

    public function testPOST()
    {
        try {
            $request = new DescribeRegionsRequest();
            $request->setMethod('POST');
            $result = self::$client->getAcsResponse($request);
            self::assertTrue(isset($result['RequestId']));
        } catch (ClientException $e) {
            self::assertStringStartsWith('cURL error ', $e->getMessage());
        } catch (ServerException $e) {
            $expected = [
                'Specified access key is not found.',
            ];
            $this->assertContains($e->getErrorMessage(), $expected);
        }
    }

    public function testBadProtocol()
    {
        try {
            $request = new DescribeRegionsRequest();
            $request->setProtocol('BadProtocol');
            $response = self::$client->getAcsResponse($request);
            $this->assertNotNull($response);
        } catch (ClientException $e) {
            self::assertStringStartsWith('cURL error ', $e->getMessage());
        } catch (ServerException $e) {
            $expected = [
                'Specified access key is not found.',
            ];
            $this->assertContains($e->getErrorMessage(), $expected);
        }
    }

    public function testBadActionName()
    {
        try {
            $request = new DescribeRegionsRequest();
            $request->action('BadActionName');
            $response = self::$client->getAcsResponse($request);
            $this->assertNotNull($response);
        } catch (ServerException $e) {
            self::assertContains(
                $e->getErrorMessage(),
                [
                    'The specified parameter "Action or Version" is not valid.',
                    'Specified access key is not found.',
                ]
            );
        } catch (ClientException $e) {
            self::assertEquals(\ALIBABA_CLOUD_SERVER_UNREACHABLE, $e->getErrorCode());
        }
    }

    public function testBadProduct()
    {
        try {
            $request = new DescribeRegionsRequest();
            $request->timeout(\ALIBABA_CLOUD_TIMEOUT);
            $request->connectTimeout(10);
            $request->product('BadProduct');
            $response = self::$client->getAcsResponse($request);
            $this->assertNotNull($response);
        } catch (ClientException $e) {
            $expected = [
                'The specified parameter "Action or Version" is not valid.',
            ];

            $this->assertContains($e->getErrorMessage(), $expected);
        } catch (ServerException $e) {
            self::assertContains(
                $e->getErrorMessage(),
                [
                    'Specified access key is not found.',
                ]
            );
        }
    }

    public function testBadVersion()
    {
        try {
            $request = new DescribeRegionsRequest();
            $request->version('BadVersion');
            $response = self::$client->getAcsResponse($request);
            $this->assertNotNull($response);
        } catch (ServerException $e) {
            self::assertContains(
                $e->getErrorMessage(),
                [
                    'Specified parameter Version is not valid.',
                    'Specified access key is not found.',
                ]
            );
        } catch (ClientException $e) {
            // Assert
            self::assertEquals(\ALIBABA_CLOUD_SERVER_UNREACHABLE, $e->getErrorCode());
        }
    }

    public function testResult()
    {
        AlibabaCloud::accessKeyClient(
            \getenv('ACCESS_KEY_ID'),
            \getenv('ACCESS_KEY_SECRET')
        )
                    ->regionId('cn-hangzhou')
                    ->asGlobalClient();

        $result = self::$client->getAcsResponse(new Result(new \GuzzleHttp\Psr7\Response));

        self::assertInstanceOf(Result::class, $result);
    }
}
