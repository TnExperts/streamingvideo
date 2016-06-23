<?php
declare(strict_types = 1);

namespace premiumwebtechnologies\streamingvideo; // use vendorname\subnamespace\classname;


/**
 * Class VideoStreamExample
 * @package premiumwebtechnologies\streamingvideo
 */
class VideoStreamExample
{

    private $pathToVideo;
    private $videoStream;
    private $contentType;

    private $startPos;
    private $endPos;
    private $fileSize;

    private $buffer = 102400;

    /**
     * VideoStreamExample constructor.
     * @param string $pathToVideo - the video file we want to stream
     * @param string $contentType - the content type of the video, defaults to video/mp4
     */
    public function __construct(string $pathToVideo, string $contentType = 'video/mp4')
    {
        $this->pathToVideo = $pathToVideo;
        $this->contentType = $contentType;
    }

    public function open() : bool
    {
        if (!($this->videoStream = fopen($this->pathToVideo, 'rb'))) {
            die('Could not open stream');
        }

        return true;

    }

    private function prepare() : bool
    {
        ob_get_clean();

        $currentStart = null;
        $currentEnd = null;

        header('Content-Type: ' . $this->contentType);
        $this->startPos = 0;
        $this->fileSize = filesize($this->pathToVideo);
        $this->endPos = $this->fileSize - 1;
        header("Accept-Ranges: 0-".$this->endPos);

        // Example $_SERVER['HTTP_RANGE']: Range: bytes=0-
        if (!isset($_SERVER['HTTP_RANGE'])) {
            header("Content-Length: " . $this->fileSize);
        } else {

            $currentStart = $this->startPos;
            $currentEnd = $this->endPos;

            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                http_response_code(416);
                header("Content-Range: bytes $this->startPos - $this->endPos / $this->fileSize");
                exit;
            }

            if ($range == '-') {
                $currentStart = $this->fileSize - substr($range, 1);
            } else {
                $range = explode('-', $range);
                $currentStart = $range[0];
                $currentEnd = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $currentEnd;
            }

            $currentEnd = ($currentEnd > $this->endPos) ? $this->endPos : $currentEnd;
            if ($currentStart > $currentEnd || $currentEnd > $this->fileSize - 1 || $currentEnd >= $this->fileSize) {
                http_response_code(416);
                header("Content-Range: bytes $this->startPos - $this->endPos / $this->fileSize");
                exit;
            }
            $this->startPos = $currentStart *1;
            $this->endPos = $currentEnd *1;

            $length = $this->endPos - $this->startPos + 1;
            fseek($this->videoStream, $this->startPos);

            http_response_code(206);
            header("Content-Length: " . $length);
            header("Content-Range: bytes $this->startPos - $this->endPos /" . $this->fileSize);
        }

        return true;

    }

    private function getVideoData()
    {
        $i = $this->startPos;
        while (!feof($this->videoStream) && $i <= $this->endPos) {
            $bytesToRead = $this->buffer;
            if (($i + $bytesToRead) > $this->endPos) {
                $bytesToRead = $this->endPos - $i + 1;
            }
            $data = fread($this->videoStream, $bytesToRead);
            yield $data;
            $i += $bytesToRead;
        }
        fclose($this->videoStream);
    }

    public function streamVideo() : bool
    {
        $this->prepare();
        $i = $this->startPos;
        set_time_limit(0);
        foreach ($this->getVideoData() as $data) {
            echo $data;
            flush();
        }
        return true;
    }
}
