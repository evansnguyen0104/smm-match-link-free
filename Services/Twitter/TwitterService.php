<?php


namespace Services\Twitter;


class TwitterService
{
    public function getTwitterObjectId($link, $type = 'follow')
    {
        $link = $this->reconstructUrl($link);
        if (strpos($link, "twitter.com")) {
            if (in_array($type, ['like'])) {
                try {
                    $re = '/https:\/\/twitter.com\/(.*)\/status\/(.*)/m';
                    preg_match_all($re, $link, $matches, PREG_SET_ORDER, 0);
                    if (isset($matches[0][2]) && isset($matches[0][2])) {
                        $objectId = str_replace("/", "", $matches[0][2]);
                        return $objectId;
                    }
                } catch (\Exception $exception) {
                    return false;
                }
            }
            if ($type == 'follow') {
                $re = '/https:\/\/twitter.com\/(.*)/m';
                preg_match_all($re, $link, $matches, PREG_SET_ORDER, 0);
                try {
                    $objectId = str_replace("/", "", $matches[0][1]);
                    return $objectId;
                } catch (\Exception $exception) {
                    return false;
                }

            }
        }
        return false;
    }

    private function reconstructUrl($url)
    {
        $url_parts = parse_url($url);
        if (isset($url_parts['scheme'])) {
            return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
        }
        return $url;
    }
}
