<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Raffle;
use App\Models\ProductImage;
use App\Models\VEmailNotifications;
use App\Models\AppConfig;
use App\Models\ContactMethod;
use App\Models\Transaction;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    const RELATIVE_PATH = 'files/products';
    
    private function absoluteFilesPath()
    {
        return public_path() . "/" . self::RELATIVE_PATH;
    }

    public function __construct(Request $request)
    {
        $this->middleware('subscribed')->except(['uploadFiles', 'executeRafflesJob', 'getFiles', 'destroyFile', 'getProductImages', 'getContactMethods']);
    }
    
    private function getUserProducts($user)
    {
        $res = [];
        if ($user)
        {
            $res = DB::select('select * from `v_products` where `user_id` = ? and `status_id` <> 0', [$user->id]);
        }
        return $res;
    }
    
    public function index(Request $request)
    {
        $id = $request->id;
        $user = User::findByApiToken($request->api_token);
        if ($id == null)
        {
            return $this->getUserProducts($user);
        }
        else
        {
            return $this->getProductByIdAndUserId($id, $user);
        }
    }
    
    private function getProductByIdAndUserId($productId, $user)
    {
        if ($user->isAdministrator())
            return Product::where(['id' => $productId])->first();
        return Product::where(['id' => $productId, 'user_id' => $user->id])->first();
    }
    
    private function getProductByRequestParams(Request $request)
    {
        $token = $request->api_token;
        $user = User::findByApiToken($token);
        return Product::where(['id' => $request->id, 'user_id' => $user->id])->first();
    }
    
    public function mainPage($id = null)
    {
        if (Auth::check())
        {
            $appConfig = AppConfig::find(1);
            if ((Auth::user()->subscribed == 1 && $appConfig->allow_raffle_creation == 1) || Auth::user()->role_id == 1)
                return view('products.index', ['filesize' => $appConfig->max_upload_filesize]);
            return view('errors.403');
        }
        return view('auth.login');
    }
    
    public function myProducts($api_token)
    {
        $user = User::findByApiToken($api_token);
        if (!$user)
            return [];
        return $this->getUserProducts($user);
    }
    
    public function search(Request $request)
    {
        $criteria = $request->criteria;
        $api_token = $request->api_token;
        
        $res = $this->getUserProducts(User::findByApiToken($api_token));
        if (isset($criteria))
        {
            $filter = [];
            $criteriaLowerCase = strtolower($criteria);
            foreach($res as $elem)
            {
                if (str_contains($elem->id . $elem->quantity . strtolower($elem->name . $elem->description . $elem->category . $elem->condition), $criteriaLowerCase))
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
    
    public function store(Request $request)
    {
        $token = $request->api_token;
        $user = User::findByApiToken($token);
        if ($user)
        {
            $this->validateObject($request);
            $obj = new Product;
            $obj->name = $request->name;
            $obj->description = $request->description;
            $obj->quantity = $request->quantity;
            $obj->condition_id = $request->condition_id;
            $obj->category_id = $request->category_id;
            $obj->contact_method_id = $request->contact_method_id;
            $obj->user_id = $user->id;
            $obj->save();
            mkdir($this->absoluteFilesPath() . "/$obj->id", 0755);
            return "Item '$obj->name' has been created.";
        }
        return "You are not allowed to perform this action";
    }
    
    private function validateObject(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:75',
            'description' => 'required|min:50|max:500',
            'quantity' => 'required|numeric|min:1',
            'category_id' => 'required|numeric',
            'condition_id' => 'required|numeric',
            'contact_method_id' => 'required|numeric',
        ]);
    }
    
    public function update(Request $request)
    {
        $obj = $this->getProductByRequestParams($request);
        if ($obj)
        {
            $this->validateObject($request);
            $obj->name = $request->name;
            $obj->description = $request->description;
            $obj->quantity = $request->quantity;
            $obj->condition_id = $request->condition_id;
            $obj->category_id = $request->category_id;
            $obj->contact_method_id = $request->contact_method_id;
            $obj->save();
            return "Item '$obj->name' has been updated.";
        }
        return "You are not authorized to edit this item";
    }
    
     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $obj = Product::find($id);
        $api_token = $request->api_token;
        $user = User::findByApiToken($api_token);
        if ($obj->user_id == $user->id || $user->isAdministrator())
        {
            $name = $obj->name;
            $raffle = Raffle::where(['product_id' => $obj->id])->first();
            if ($raffle)
            {
                $transactions = Transaction::where(['raffle_id' => $raffle->id])->get();
                if ($transactions)
                {
                    $appConfig = AppConfig::find(1);
                    foreach ($transactions as $transaction)
                    {
                        $paymentCtrl = new PaymentController();
                        $transaction->amount_to_refund = StaticMembersController::perCent($transaction->amount_to_pay, 100 - $appConfig->paypal_fee);
                        $result = $paymentCtrl->makeRefund($transaction->paypal_transaction_id, $transaction->amount_to_refund);
                        $transaction->transaction_status_id = (str_contains($result, 'An error has occurred')) ? 3 : 4;
                        $transaction->save();
                    }
                }
            }
            $obj->delete();
            $this->removeDirectory($this->absoluteFilesPath() . "/$id/");
            return "Item '$name' has been deleted.";
        }
        return "You are not authorized to delete this item";
    }
    
    public function uploadFiles(Request $request)
    {
        $id = $request->product_id;
        $files = $_FILES;
        $relativePath = self::RELATIVE_PATH . "/$id/";
        $absolutePath = $this->absoluteFilesPath() . "/$id/";
        if (!file_exists($absolutePath))
        {
            mkdir($absolutePath, 0755);
        }
        $index = 0;
        $res = ['files' => []];
        $appConfig = AppConfig::find(1);
        foreach ($files as $file)
        {
            if ($file['size'][$index] <= $appConfig->max_upload_filesize * 1000)
            {
                $tempName = $file['tmp_name'][$index];
                $realName = $file['name'][$index++];
                $fullFilePath = $absolutePath . $realName;
                try
                {
                    if (move_uploaded_file($tempName, $fullFilePath))
                    {
                        $image = new ProductImage();
                        $image->product_id = $id;
                        $image->image_path = $relativePath . $realName;
                        $image->name = $realName;
                        $image->size = filesize($fullFilePath);
                        $image->url = url("/$relativePath$realName");
                        $image->thumbnailUrl = $image->url;
                        $image->deleteType = "DELETE";
                        $image->save();
                        $image->deleteUrl = url("/api/v1/products/upload-files/$image->id");
                        $image->update();
                        $res['files'][] = $image;
                    }
                }
                catch(\Exception $exc)
                {
                    $res['files'][] = ['name' => substr($exc->getMessage(), 0, 1000), 'thumbnailUrl' => url("/assets/img/error.gif")];
                }
            }
            else
            {
                $res['files'][] = ['name' => 'Filesize exceeds maximum allowed.', 'thumbnailUrl' => url("/assets/img/error.gif")];
            }
        }
        return $res;
    }
    
    public function destroyFile(Request $request)
    {
        $obj = ProductImage::find($request->id);
        $path = public_path() . "/$obj->image_path";
        $obj->delete();
        if (file_exists($path))
            unlink($path);
        return [$obj->name => true];
    }
    
    function removeDirectory($dir)
    {
        if (file_exists($dir))
        {
            foreach(glob($dir . '/*') as $file)
            {
                if(is_dir($file))
                    rrmdir($file);
                else
                    unlink($file);
            }
            rmdir($dir);
        }
    }
    
    public function getFiles(Request $request)
    {
        $pp = $request->product_id;
        $pp2 = $request->api_token;
        
        $res['files'] = $this->getProductImages($request->product_id);
        return $res;
    }
    
    function getProductImages($product_id)
    {
        return ProductImage::where('product_id', $product_id)->get();
    }
    
    function executeRafflesJob()
    {
        DB::delete('delete from `traces`');
        DB::insert('insert into `traces` (`value`) values (now())');
        $notifications = VEmailNotifications::get();
        $sentNotificationsIds = '';
        foreach ($notifications as $notif)
        {
            $res = StaticMembersController::sendRaffleExecutionAlert($notif);
            if ($res)
            {
                $sentNotificationsIds .= "$notif->id,";
            }
        }
        
        if ($sentNotificationsIds)
        {
            $sentNotificationsIds = trim($sentNotificationsIds, ",");
            $deletedRows = DB::delete("delete from `users_notifications` where `id` in ($sentNotificationsIds)");
        }
    }
    
    public function getContactMethods()
    {
        return ContactMethod::orderBy('name', 'asc')->get();
    }
}
