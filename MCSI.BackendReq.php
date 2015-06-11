<?php
/**
 * HTTP requester for Moegirl Client Service Infrastructure backend
 *
 * @file
 * @ingroup Extensions
 */
 
 class MCSIBackendReq {
     public static function invalidatePageCache($title)
     {
         global $wgMCSIServerUrl;
     
         $escaped_title = rawurlencode($title);
         $url = "{$wgMCSIServerUrl}/api/Cache/Page/?title={$escaped_title}";
         $curl = curl_init($url);
         self::sendRequest($url, "PUT");
     }
     
     public static function deletePageCache($title)
     {
         global $wgMCSIServerUrl;
     
         $escaped_title = rawurlencode($title);
         $url = "{$wgMCSIServerUrl}/api/Cache/Page/?title={$escaped_title}";
         self::sendRequest($url, "DELETE");
     }
     
     public static function invalidateTemplateCache($title)
     {
         global $wgMCSIServerUrl;
     
         $escaped_title = rawurlencode($title);
         $url = "{$wgMCSIServerUrl}/api/Cache/Template/?template={$escaped_title}";
         self::sendRequest($url, "PUT");
     }
     
     private static function sendRequest($url, $method, $content = null, $content_type = 'text/plain')
     {
         global $wgMCSIServerAppKey;
         $curl = curl_init($url);
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
         curl_setopt($curl, CURLOPT_HEADER, false);
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
         
         if (is_null($content)) {
             curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-ZUMO-APPLICATION: {$wgMCSIServerAppKey}",
                                                      'Content-length: 0'));
         } else {
             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
             curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-ZUMO-APPLICATION: {$wgMCSIServerAppKey}",
                                                          'Content-length: ' . strlen($content)),
                                                          'Content-Type: ' . $content_type);
         }
         
         curl_exec($curl);
         curl_close($curl);
     }

 }
 