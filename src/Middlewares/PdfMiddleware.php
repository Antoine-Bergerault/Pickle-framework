<?php

class PdfMiddleware{

    public function render(string $key){
        $src = ROOT.'/data/'.$key.'.pdf';

        $fp = fopen($src, 'rb');
        
        $ctype = 'application/pdf';
        $filesize = filesize($src);

        header('Content-type: ' . $ctype);
        header("Content-Length: " . $filesize);
        header("Content-Range: 0-".($filesize-1)."/".$filesize);
        header('Pragma: public');
        header('Cache-Control: public, max-age=31536000');
        header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
        header('Accept-Ranges: bytes');
        header('access-control-allow-origin: *');
        header('access-control-allow-methods: GET, OPTIONS');

        fpassthru($fp);
        return $key;
    }

}