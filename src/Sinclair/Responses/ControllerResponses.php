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
    protected function doStore($createRequest, $route = null, $routeParams = null)
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
    protected function doUpdate($updateRequest, Model $model, $route = null, $routeParams = null)
    {
        try
        {
            $result = true;

            $this->repository->update($updateRequest->all(), $model);
        }
        catch (\Exception $e)
        {
            $result = false;
        }

        return $this->crudResponse($result, $route, $routeParams);
    }

    /**
     * respond to a crud add/update
     *
     * @param $resultOfRepositoryAction
     * @param string $route
     *
     * @param $routeParams
     * @param null $message
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function crudResponse($resultOfRepositoryAction, $route = null, $routeParams = null, $message = null)
    {
        return $this->isAjax() ?
            $resultOfRepositoryAction ?
                SinclairResponse::jsonSuccess(array_merge($this->successMessage($message), ['data' => $resultOfRepositoryAction])) :
                SinclairResponse::jsonFailure($this->failureMessage($message)) :
            $this->redirectToRoute($route, $routeParams, $message);
    }

    protected function redirectToRoute($route, $routeParams, $message)
    {
        return redirect()
            ->route($this->getRoute($route), $routeParams)
            ->with('message', $message);
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        $class = strtolower(str_replace('Controller', '', class_basename($this)));

        return $this->prefix != null ? $this->prefix . '.' . $class : $class;
    }

    /**
     * @param $route
     *
     * @return string
     */
    protected function getRoute($route)
    {
        return $route == null ? $this->getRouteName() . '.index' : $route;
    }

    /**
     * @param $message
     * @param $default
     *
     * @return mixed
     */
    protected function setMessage(&$message, $default)
    {
        return $message == null ? $default : $message;
    }

    /**
     * @return bool
     */
    protected function isAjax()
    {
        return Request::ajax() || Request::wantsJson();
    }

    /**
     * @param $message
     *
     * @return array
     */
    protected function successMessage(&$message)
    {
        $message = $this->setMessage($message, 'Your request was processed successfully.');

        return compact('message');
    }

    /**
     * @param $message
     *
     * @return array
     */
    protected function failureMessage(&$message)
    {
        $message = $this->setMessage($message, 'Your request failed to process, please try again.');

        return compact('message');
    }
}