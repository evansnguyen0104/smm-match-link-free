<?php

namespace Services\Instagram;


class InstagramService
{
    protected function reconstructUrl($url)
    {
        $url_parts = parse_url($url);
        if (isset($url_parts['scheme'])) {
            return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
        }
        return $url;
    }

    public function getInstagramObjectIdFromLink($link, $type = 'follow')
    {
        $objectId = false;
        $link = $this->reconstructUrl($link);
        if (!strpos($link, "instagram.com")) {
            return $objectId;
        }
        switch ($type) {
            case "like":
            case "comment":
                try {
                    preg_match('/(http[s]?:\/\/)?([^\/\s]+\/)(.\/)(.*)/', $link, $post_id);
                    $objectId = str_replace("/", "", $post_id[4]);
                } catch (\Exception $exception) {
                }
                break;
            case "view":
                try {
                    preg_match('/https:\/\/www\.instagram\.com\/tv\/(.*)/', $link, $post_id, PREG_OFFSET_CAPTURE, 0);
                    if (isset($post_id[1][0])) {
                        $objectId = str_replace("/", "", $post_id[1][0]);
                    }
                } catch (\Exception $exception) {
                }
                break;
            case "follow":
                preg_match('/(http[s]?:\/\/)?([^\/\s]+\/)(.*)/', $link, $profile_id);
                try {
                    $objectId = str_replace("/", "", $profile_id[3]);
                } catch (\Exception $exception) {
                }
                break;
            default:
        }
        return $objectId;
    }
}
