<?php


namespace Services\Youtube;


class YoutubeService
{
    public function getObjectIdFromUrl($url, $type)
    {
        $objectId = false;
        switch ($type) {
            case "like":
            case "comment":
            case "view":
                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
                if (isset($match[1])) $objectId = $match[1];
                break;
            case "sub":
            case "follow":
                $objectId = $this->parseChannelId($url);
                break;
            default:
                $objectId = false;
        }
        return $objectId;
    }

    protected function parseChannelId(string $url): ?string
    {
        $parsed = parse_url(rtrim($url, '/'));
        if (isset($parsed['path']) && preg_match('/^\/channel\/(([^\/])+?)$/', $parsed['path'], $matches)) {
            return $matches[1];
        }
        return false;
    }
}
