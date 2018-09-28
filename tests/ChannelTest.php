<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 9/28/18
 * Time: 2:09 PM
 */

use LCI\Salsify\Channel;

class ChannelTest extends BaseTestCase
{
    protected $channel_id = CHANNEL_ID;


    public function testGetChannelData()
    {
        $channel = new Channel(self::getApiInstance());

        try {
            $salsifyResponse = $channel->getChannelData($this->channel_id);

        } catch (GuzzleHttp\Exception\GuzzleException $exception) {
            $this->assertEmpty(
                $exception->getMessage(),
                'Check that your CHANNEL_ID in the config is correct: ' . $exception->getMessage()
            );
        }

        $this->assertEquals(
            '200',
            $salsifyResponse->getStatusCode(),
            'Failed $channel->getChannelData '.$salsifyResponse->getReasonPhrase(). PHP_EOL.
            $salsifyResponse->getBody()
        );

        file_put_contents(__DIR__ . '/temp/channel_data_' . $this->channel_id, $salsifyResponse->getBody());
    }

    /**
     * @depends testGetChannelData
     */
    public function testSaveLatestChannel()
    {
        $channel = new Channel(self::getApiInstance());

        $file = __DIR__ . '/temp/save_channel_data_' . $this->channel_id . '.json';

        try {
            $filename = $channel->saveLatestChannel($this->channel_id, $file);

        } catch (GuzzleHttp\Exception\GuzzleException $exception) {
            $this->assertEmpty(
                $exception->getMessage(),
                'Check that your CHANNEL_ID in the config is correct: ' . $exception->getMessage()
            );
        }

        $this->assertEquals(
            $file,
            $filename,
            'Failed $channel->saveLatestChannel '
        );

        $this->assertFileExists(
            $filename,
            'File did not save'
        );
    }
}