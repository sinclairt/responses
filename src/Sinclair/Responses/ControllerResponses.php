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
     * @param null $message
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function doStore( $createRequest, $route = null, $routeParams = null, $message = null )
    {
        return $this->crudResponse($this->repository->add($createRequest->all()), $route, $routeParams, $message);
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
     * @param null $message
     *
     * @return mixed
     */
    protected function doUpdate( $updateRequest, Model $model, $route = null, $routeParams = null, $message = null )
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

        return $this->crudResponse($result, $route, $routeParams, $message);
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
        $message = $this->setMessage($message, trans('responses::responses.success.message'));

        return compact('message');
    }

    /**
     * @param $message
     *
     * @return array
     */
    protected function failureMessage( &$message )
    {
        // we don't want to send the users success message as an error
        $message = null;

        $message = $this->setMessage($message, trans('responses::responses.failure.message'));

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
        $class = snake_case($this->getResourceName());

        return $this->prefix != null ? $this->prefix . '.' . $class : $class;
    }

    /**
     * @return mixed
     */
    protected function getResourceName()
    {
        return str_replace('Controller', '', class_basename($this));
    }
}