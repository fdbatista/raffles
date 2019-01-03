<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\AppConfig;
use Illuminate\Support\Facades\DB;
use Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CategoriesController extends Controller
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
                return view('categories.index');
            throw new AccessDeniedHttpException;
        }
        $appConfig = AppConfig::find(1);
        return view('auth.login', ['app_title' => $appConfig->app_title]);
    }
    
    public function index($id = null)
    {
        if ($id == null)
        {
            $res = Category::orderBy('name', 'asc')->get();
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
        return Category::find($id);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function search($criteria)
    {
        $res = Category::orderBy('name', 'asc')->get();
        if (isset($criteria) && $criteria != 'undefined')
        {
            $filter = [];
            $criteriaLower = strtolower($criteria);
            foreach($res as $elem)
            {
                if (str_contains($elem->id . strtolower($elem->name . $elem->description), $criteriaLower))
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
        $category = new Category;
        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->save();
        return "Category '$category->name' has been created.";
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
        $category = Category::find($id);
        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->save();
        return "Category '$category->name' has been updated.";
    }
    
     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $used = DB::select('select 1 from `products` where `category_id` = ? limit 1', [$id]);
        if (!$used)
        {
            $category = Category::find($id);
            $name = $category->name;
            $category->delete();
            return "Category '$name' has been deleted.";
        }
        return "This item cannot be deleted.";
    }
    
}
