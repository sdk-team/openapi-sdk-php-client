<?php

namespace AlibabaCloud\Client\Tests\Framework;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\DefaultAcsClient;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Client\Profile\DefaultProfile;
use AlibabaCloud\Client\Tests\Mock\Services\Ecs\DescribeRegionsRequest;
use PHPUnit\Framework\TestCase;

/**
 * Test the compatibility of the new SDK with the old SDK.
 *
 * @package   AlibabaCloud\Client\Tests\Framework
 *
 * @author    Alibaba Cloud SDK <sdk-team@alibabacloud.com>
 * @copyright 2019 Alibaba Group
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @link      https://github.com/aliyun/openapi-sdk-php-client
 */
class RequestCompatibilityTest extends TestCase
{
    public function testGetAcsResponse()
    {
        // Setup
        $regionId = 'cn-hangzhou';
        $key      = \getenv('ACCESS_KEY_ID');
        $secret   = \getenv('ACCESS_KEY_SECRET');
        AlibabaCloud::accessKeyClient($key, $secret)->name('test');

        // Test
        $profile = DefaultProfile::getProfile($regionId, $key, $secret);

        $client  = new DefaultAcsClient($profile);
        $request = new DescribeRegionsRequest();
        try {
            $result = $client->getAcsResponse($request->client('test'));
            // Assert
            self::assertNotEquals($result->getRequest()->client, 'test');
        } catch (ClientException $e) {
            self::assertEquals(\ALIBABA_CLOUD_SERVER_UNREACHABLE, $e->getErrorCode());
        } catch (ServerException $e) {
            if (\getenv('ACCESS_KEY_ID') === 'foo') {
                $this->assertEquals(
                    $e->getErrorMessage(),
                    'Specified access key is not found.'
                );
            }
        }
    }

    public function testGetAcsResponseWithResult()
    {
        // Setup
        $regionId = 'cn-hangzhou';
        $key      = \getenv('ACCESS_KEY_ID');
        $secret   = \getenv('ACCESS_KEY_SECRET');
        AlibabaCloud::accessKeyClient($key, $secret)->regionId($regionId)
                    ->name('test');

        // Test
        $profile = DefaultProfile::getProfile($regionId, $key, $secret);
        $client  = new DefaultAcsClient($profile);
        $request = new DescribeRegionsRequest();
        try {
            $result = $client->getAcsResponse($request->client('test')->request());
            // Assert
            self::assertEquals($result->getRequest()->client, 'test');
        } catch (ClientException $e) {
            self::assertEquals(\ALIBABA_CLOUD_SERVER_UNREACHABLE, $e->getErrorCode());
        } catch (ServerException $e) {
            if (\getenv('ACCESS_KEY_ID') === 'foo') {
                $this->assertEquals(
                    $e->getErrorMessage(),
                    'Specified access key is not found.'
                );
            } else {
                $this->assertEquals(
                    $e->getErrorMessage(),
                    'InvalidApi.NotPurchase'
                );
            }
        }
    }

    /**
     * Clear sharing settings.
     */
    public function tearDown()
    {
        parent::tearDown();
        AlibabaCloud::flush();
    }
}
