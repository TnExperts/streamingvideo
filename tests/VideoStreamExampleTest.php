<?php
/**
 * Created by PhpStorm.
 * User: kevindavies
 * Date: 23/06/16
 * Time: 3:45 PM
 */

// Configure this file by going to Preferences -> Editor -> File and Code Templates
// To set coding standard go to Preferences -> Editor -> Inspections -> PHP -> PHP Code Sniffer validation
// [in coding standards drop down select desired coding standard]
// To set PHP version to use click on External Libraries on left pane and select Configure PHP include paths.
// To verify this file go to Code -> Inspect code
// To view error log go to /Applications/MAMP/logs
// To install a composer package right click on project name, click on composer and select init. Once done select
// composer again and select add dependency.
// To add a bookmark click fn+F3
// To show bookmarks click fn+cmd+F3
// To go to a bookmark click ctl+[0=9]
// To format cmd+alt+L
// To duplicate a line(s): cmd+d

include 'src/VideoStreamExample.php';


class VideoStreamExampleTest extends PHPUnit_Framework_TestCase
{

    public function testVideoStream()
    {
        $streamer = new \premiumwebtechnologies\streamingvideo\VideoStreamExample('video.mp4');
        $this->assertTrue(true, $streamer->streamVideo());
    }

}
