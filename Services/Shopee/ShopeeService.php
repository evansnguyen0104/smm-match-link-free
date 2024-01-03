<?php


namespace Services\Shopee;


class ShopeeService
{
    public function getObjectIdFromShopeeLink($link, $type = 'like')
    {
        $link = str_replace("http://", "https://", $link);
        if (strpos($link, "shp.ee")) {
            $link = $this->getRedirectUrl($link);
            if (strpos($link, "universal-link")) {
                $link = $this->getValueFromStringUrl($link, 'redir');
                if ($link) {
                    $link = $this->reconstructUrl($link);
                }
            }
        }
        if (strpos($link, "shopee.vn")) {
            if (in_array($type, ['like'])) {
                try {
                    $link = substr($link, strpos($link, "-i") + 1);
                    preg_match_all('!\d+!', $link, $matches);
                    if (isset($matches[0][0]) && isset($matches[0][1])) {
                        $shopId = str_replace("/", "", $matches[0][0]);
                        $objectId = str_replace("/", "", $matches[0][1]);
                        return [
                            'shop_id' => $shopId,
                            'object_id' => $objectId
                        ];
                    }
                } catch (\Exception $exception) {
                    return false;
                }
            }
            if ($type == 'follow') {
                preg_match('/(http[s]?:\/\/)?([^\/\s]+\/)(.*)/', $link, $profile_id);
                try {
                    $objectId = str_replace("/", "", $profile_id[3]);
                    return [
                        'object_id' => $objectId
                    ];
                } catch (\Exception $exception) {
                    return false;
                }

            }
        }
        return false;
    }

    /**
     * getRedirectUrl()
     * Gets the address that the provided URL redirects to,
     * or FALSE if there's no redirect.
     *
     * @param string $url
     * @return string
     */
    protected function getRedirectUrl($url)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseHeaders = curl_exec($ch);
        curl_close($ch);
        if (preg_match_all("/^location: (.*?)$/im", $responseHeaders, $results))
            return $results[1][0];
        return $url;
    }

    public function getValueFromStringUrl($url, $parameter_name)
    {
        $parts = parse_url($url);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query[$parameter_name])) {
                return $query[$parameter_name];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    protected function reconstructUrl($url)
    {
        $url_parts = parse_url($url);
        if (isset($url_parts['scheme'])) {
            return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
        }
        return $url;
    }
}
