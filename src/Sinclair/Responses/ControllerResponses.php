<?php

namespace Sinclair\Responses;

use Request;
use Illuminate\Database\Eloquent\Model;

trait ControllerResponses
{

    /**
     * Store a newly created resource in storage.
     * write the store() method in your own class and inject the proper request object
     *
     * usage:
     *
     * store(MyRequest $request){
     *  return $this->doStore($request);
     * }
     *
     * @param param Request $createRequest
     * @param string $route
     *
     * @param null $routeParams
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function doStore( $createRequest, $route = null, $routeParams = null )
    {
        return $this->crudResponse($this->repository->add($createRequest->all()), $route, $routeParams);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param param Request $updateRequest
     * @param Model $model
     *
     * @param string $route
     *
     * @param null $routeParams
     *
     * @return mixed
     */
    protected function doUpdate( $updateRequest, Model $model, $route = null, $routeParams = null )
    {
        try
        {
            $result = true;

            $this->repository->update($updateRequest->all(), $model);
        }
        catch ( \Exception $e )
        {
            $result = false;
        }

        return $this->crudResponse($result, $route, $routeParams);
    }

    /**
     * respond to a crud add/update
     *
     * @param $result
     * @param string $route
     *
     * @param $routeParams
     * @param null $message
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function crudResponse( $result, $route = null, $routeParams = null, $message = null )
    {
        return $this->isAjax() ?
            $this->getAjaxResponse($result, $message) :
            $this->redirectToRoute($route, $routeParams, $this->getMessage($result, $message));
    }

    /**
     * @return bool
     */
    protected function isAjax()
    {
        return Request::ajax() || Request::wantsJson();
    }

    /**
     * @param $result
     *
     * @param $message
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function getAjaxResponse( $result, $message )
    {
        return $result ?
            SinclairResponse::jsonSuccess(array_merge($this->successMessage($message), [ 'data' => $result ])) :
            SinclairResponse::jsonFailure($this->failureMessage($message));
    }

    /**
     * @param null $route
     * @param null $routeParams
     * @param null $message
     *
     * @return mixed
     */
    protected function redirectToRoute( $route = null, $routeParams = null, $message = null )
    {
        return redirect()
            ->route($this->getRoute($route), $routeParams)
            ->with('message', $message);
    }

    /**
     * @param $result
     * @param null $message
     *
     * @return null
     */
    private function getMessage( $result, $message = null )
    {
        if ( is_null($message) )
            $result ? $this->successMessage($message) : $this->failureMessage($message);

        return $message;
    }

    /**
     * @param $message
     *
     * @return array
     */
    protected function successMessage( &$message )
    {
        $message = $this->setMessage($message, 'Your request was processed successfully.');

        return compact('message');
    }

    /**
     * @param $message
     *
     * @return array
     */
    protected function failureMessage( &$message )
    {
        $message = $this->setMessage($message, 'Your request failed to process, please try again.');

        return compact('message');
    }

    /**
     * @param $message
     * @param $default
     *
     * @return mixed
     */
    protected function setMessage( &$message, $default )
    {
        return $message == null ? $default : $message;
    }

    /**
     * @param $route
     *
     * @return string
     */
    protected function getRoute( $route )
    {
        return $route == null ? $this->getRouteName() . '.index' : $route;
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        $class = strtolower(str_replace('Controller', '', class_basename($this)));

        return $this->prefix != null ? $this->prefix . '.' . $class : $class;
    }
}