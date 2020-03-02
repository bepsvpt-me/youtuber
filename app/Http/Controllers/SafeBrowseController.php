<?php

namespace App\Http\Controllers;

use ErrorException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class SafeBrowseController extends Controller
{
    /**
     * YouTube image.
     *
     * @param string $payload
     *
     * @return BinaryFileResponse
     */
    public function ytimg(string $payload): BinaryFileResponse
    {
        $uid = $this->decrypt($payload);

        $url = sprintf('https://i.ytimg.com/vi/%s/hqdefault.jpg', $uid);

        $path = storage_path(sprintf('ytimg/%s.jpg', md5($url)));

        return $this->response($path, $url);
    }

    /**
     * Google user photo.
     *
     * @param string $payload
     *
     * @return BinaryFileResponse
     */
    public function ggpht(string $payload): BinaryFileResponse
    {
        $url = $this->decrypt($payload);

        abort_unless(Str::startsWith($url, 'https://yt3.ggpht.com/a'), 404);

        $path = storage_path(sprintf('ggpht/%s.jpg', md5($url)));

        return $this->response($path, $url);
    }

    /**
     * Decrypt encrypt payload.
     *
     * @param string $payload
     *
     * @return string
     */
    protected function decrypt(string $payload): string
    {
        $plain = app('aes')->decrypt(hex2bin($payload));

        abort_if(empty($plain), 404);

        return $plain;
    }

    /**
     * Safe Browse response.
     *
     * @param string $path
     * @param string $url
     *
     * @return BinaryFileResponse
     */
    protected function response(string $path, string $url): BinaryFileResponse
    {
        if (!File::isReadable($path)) {
            $this->fetch($url, $path);
        }

        if (request('type', 'webp') === 'webp') {
            if (File::isReadable($this->extensionToWebp($path))) {
                $path = $this->extensionToWebp($path);
            }
        }

        $cache = [
            'immutable' => true,
            'max_age' => 60 * 60 * 24 * 7,
            'public' => true,
        ];

        return response()
            ->file($path, ['content-type' => mime_content_type($path)])
            ->setCache($cache);
    }

    /**
     * Fetch and store remote file.
     *
     * @param string $url
     * @param string $path
     *
     * @return bool
     */
    protected function fetch(string $url, string $path): bool
    {
        try {
            $content = file_get_contents($url);
        } catch (ErrorException $e) {
            abort(404);
        }

        abort_unless($content, 404);

        $ok = boolval(File::put($path, $content, true));

        if (!$ok) {
            return false;
        }

        return imagewebp(
            imagecreatefromjpeg($path),
            $this->extensionToWebp($path),
            60
        );
    }

    /**
     * Replace jpg extension to webp.
     *
     * @param string $path
     *
     * @return string
     */
    protected function extensionToWebp(string $path): string
    {
        if (!Str::endsWith($path, '.jpg')) {
            return $path;
        }

        $end = strrpos($path, '.jpg');

        return sprintf('%s.webp', substr($path, 0, $end));
    }
}
