<?php


namespace Services\Tiktok;


class TiktokService
{
    protected function reconstructUrl($url)
    {
        $url_parts = parse_url($url);
        if (isset($url_parts['scheme'])) {
            return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
        }
        return $url;
    }

    public function getTiktokObjectId($link, $type = 'like')
    {
        $link = $this->reconstructUrl($link);
        if (strpos($link, "tiktok.com")) {
            if (in_array($type, ['like', 'view', 'comment', 'share'])) {
                $re = '/https:\/\/www.tiktok.com\/@(.*)\/video\/(.*)/m';
                preg_match_all($re, $link, $matches, PREG_SET_ORDER, 0);
                if (isset($matches[0][2])) {
                    return $matches[0][2];
                }
            }
            if (in_array($type, ['follow'])) {
                $re = '/https:\/\/www.tiktok.com\/@(.*)/m';
                preg_match_all($re, $link, $matches, PREG_SET_ORDER, 0);
                if (isset($matches[0][1])) {
                    return $matches[0][1];
                }
            }
        }
        return false;
    }
}
