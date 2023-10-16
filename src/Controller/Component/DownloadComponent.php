<?php

declare(strict_types=1);

/**
 * Download Component
 *
 * This component handles sending download responses from the application.
 */

namespace App\Controller\Component;

use App\Exception\FileEmptyDownloadException;
use Cake\Controller\Component;
use Cake\Http\CallbackStream;
use Cake\Http\Response;

class DownloadComponent extends Component
{
	/**
	 * Build a Cake Response to download a file
	 *
	 * @param string $name The name of the file to download, including extension.
	 * @param int $size The size of the file in bytes.
	 * @param resource|string $contents The contents of the file in its entirety.
	 * @return \Cake\Http\Response
	 */
	public function downloadFile(string $name, int $size, mixed $contents): Response
	{
		return $this->getResponse($name, $size, $contents);
	}

	/**
	 * Build a Cake Response to preview a file (inline, not force download)
	 *
	 * @param string $name The name of the file to download, including extension.
	 * @param int $size The size of the file in bytes.
	 * @param resource|string $contents The contents of the file in its entirety.
	 * @param null $mimeType Optional mime-type to render file in browser, otherwise will force download.
	 * @return \Cake\Http\Response
	 */
	public function previewFile(string $name, int $size, mixed $contents, string $mimeType): Response
	{
		return $this->getResponse($name, $size, $contents, $mimeType);
	}

	/**
	 * Build a Cake Response to download a file
	 *
	 * @param string $name The name of the file to download, including extension.
	 * @param int $size The size of the file in bytes.
	 * @param mixed|null $contents The contents of the file in its entirety.
	 * @param string|null $mime Optional mime-type to render file in browser, otherwise will force download.
	 * @return \Cake\Http\Response
	 */
	private function getResponse(string $name, int $size, $contents, $mime = null): Response
	{
		// Ensure we have something to send
		if (empty($contents)) {
			throw new FileEmptyDownloadException();
		}

		// Adjust PHP settings to prevent any errors or timeouts
		$this->setPhpSettings();

		// Get base response from controller
		$response = $this->getController()->getResponse();

		// Set up the response headers
		$response = $response
			->withHeader('Pragma', 'no-cache')
			->withHeader('Cache-Control', 'no-cache, must-revalidate')
			->withHeader('Content-Transfer-Encoding', 'binary')
			->withHeader('Content-Length', (string)$size)
			->withHeader('x-filename', $name);

		// Force Download option
		if (empty($mime) || $mime == null || $mime === true) {
			$response = $response
				->withDownload($name)
				->withType('application/octet-stream');
		} else {
			$response = $response
				->withHeader('Content-Disposition', 'inline; filename="' . $name . '"')
				->withType($mime);
		}

		/**
		 * Attach the file contents as the response body
		 *
		 * @var \Cake\Http\Response $response
		 */
		$response = $this->setResponseBody($response, $contents);

		// Return the response object
		return $response;
	}

	/**
	 * Adjust PHP settings to prevent any errors or timeouts
	 *
	 * @return void
	 */
	private function setPhpSettings(): void
	{
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '300');
		ini_set('zlib.output_compression', 'Off');
	}

	/**
	 * Check type of contents and set consistent response body
	 *
	 * @param \App\Controller\Component\Cake\Http\Response $response
	 * @param mixed $contents
	 * @return \App\Controller\Component\Cake\Http\Response
	 */
	private function setResponseBody(Response $response, mixed $contents): Response
	{
		// fopen() handle (resource)
		if (is_resource($contents)) {
			$stream = new CallbackStream(function () use ($contents): void {

				$metaData = stream_get_meta_data($contents);
				if ($metaData['seekable']) {
					rewind($contents);
				}

				ob_start();
				fpassthru($contents);
				//echo stream_get_contents($contents);
				fclose($contents);
			});

			return $response->withBody($stream);
		}

		// Raw file contents (already ran stream_get_contents or file_get_contents...)
		if (is_string($contents)) {
			return $response->withStringBody($contents);
		}

		// A function (like a callback)
		if (is_callable($contents)) {
			return $response->withBody(
				call_user_func($contents, 'getContents')
			);
		}

		// Default to sending back whatever was provided
		return $response->withStringBody($contents);
	}
}
