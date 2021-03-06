<?php

/*
 * This file is part of the Flysystem HTTP Downloader package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Flysystem;

use League\Flysystem\Filesystem;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Psr\Http\Message\RequestInterface;

/**
 * HTTP Downloader
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Downloader
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var HttpAdapterInterface
     */
    protected $httpAdapter;

    /**
     * @param Filesystem           $filesystem
     * @param HttpAdapterInterface $httpAdapter
     */
    public function __construct(Filesystem $filesystem, HttpAdapterInterface $httpAdapter)
    {
        $this->filesystem = $filesystem;
        $this->httpAdapter = $httpAdapter;
    }

    /**
     * Downloads a request
     *
     * @param RequestInterface $request
     *
     * @return boolean
     */
    public function download(RequestInterface $request, $path)
    {
        $response = $this->httpAdapter->sendRequest($request);

        if (!$body = $response->getBody()) {
            return false;
        }

        $stream = $body->detach();

        if (is_resource($stream)) {
            return $this->filesystem->putStream($path, $stream);
        }

        return $this->filesystem->put($path, $stream);
    }
}
