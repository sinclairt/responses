<?php namespace Sterling\Responses;

use Illuminate\Support\Facades\Response as LaravelResponse;
use Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lang;

define('AJAX_RESPONSE_STATUS_SUCCESS', 'success');
define('AJAX_RESPONSE_STATUS_FAILURE', 'failure');

class SterlingResponse extends LaravelResponse
{

    /**
     * @param $status
     * @param array $args
     * @param int $code
     *
     * @return JsonResponse
     * @throws \Exception
     */
	public static function jsonStatusResponse($status, $args = [ ], $code = 200)
	{
		$response_array = [
			'status'  => $status,
			'success' => $status == AJAX_RESPONSE_STATUS_SUCCESS,
			// convenience boolean var
			'title'   => Lang::get('responses.' . $status . '.title'),
			'message' => Lang::get('responses.' . $status . '.message')
		];

		if (count($args) == 1 && is_string(reset($args))){
			$response_array[ 'message' ] = reset($args);
		}else{
            if(!is_array(reset($args))) throw new \Exception("Array not provided to SterlingResponse");
            $response_array = array_merge($response_array, reset($args));
        }

		$jsonResponse = new JsonResponse($response_array, $code);

		Log::info($jsonResponse);

		return $jsonResponse;
	}

	/**
	 * @return JsonResponse
	 */
	public static function jsonSuccess()
	{
		$args = func_get_args();

		return self::jsonStatusResponse(AJAX_RESPONSE_STATUS_SUCCESS, $args, 200);
	}

	/**
	 * @return JsonResponse
	 */
	public static function jsonFailure()
	{
		$args = func_get_args() ?: [];

		return self::jsonStatusResponse(AJAX_RESPONSE_STATUS_FAILURE, $args, 422);
	}

}