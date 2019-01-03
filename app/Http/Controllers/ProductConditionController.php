<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductCondition;
use App\Models\AppConfig;
use Illuminate\Support\Facades\DB;
use Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProductConditionController extends Controller
{
    private $adminActions = ['store', 'update', 'destroy'];
    
    public function __construct(Request $request)
    {
        $token = $request->input('api_token');
        $actionName = StaticMembersController::parseActionName($this->getRouter()->currentRouteAction());
        if (in_array($actionName, $this->adminActions))
        {
            $user = \App\User::findByApiToken($token);
            if (!$user || !$user->isAdministrator())
            {
                throw new AccessDeniedHttpException;
            }
        }
    }
    
    public function mainPage()
    {
        $user = Auth::user();
        if ($user)
        {
            if ($user->isAdministrator())
                return view('product-conditions.index');
            throw new AccessDeniedHttpException;
        }
        $appConfig = AppConfig::find(1);
        return view('auth.login', ['app_title' => $appConfig->app_title]);
    }
    public function index($id = null)
    {
        if ($id == null)
        {
            $res = ProductCondition::orderBy('name', 'asc')->get();
            return $res;
        }
        else
        {
            return $this->show($id);
        }
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return ProductCondition::find($id);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function search($criteria)
    {
        $res = ProductCondition::orderBy('name', 'asc')->get();
        if (isset($criteria) && $criteria != 'undefined')
        {
            $filter = [];
            $criteriaLower = strtolower($criteria);
            foreach($res as $elem)
            {
                if (str_contains($elem->id . strtolower($elem->name), $criteriaLower))
                {
                    $filter[] = $elem;
                }
            }
            return $filter;
        }
        else
        {
            return $res;
        }
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $obj = new ProductCondition;
        $obj->name = $request->input('name');
        $obj->save();
        return "Item '$obj->name' has been created.";
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $obj = ProductCondition::find($id);
        $obj->name = $request->input('name');
        $obj->save();
        return "Item '$obj->name' has been updated.";
    }
    
     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $used = DB::select('select 1 from `products` where `condition_id` = ? limit 1', [$id]);
        if (!$used)
        {
            $obj = ProductCondition::find($id);
            $name = $obj->name;
            $obj->delete();
            return "Item '$name' has been deleted.";
        }
        return "This item cannot be deleted.";
    }
}
