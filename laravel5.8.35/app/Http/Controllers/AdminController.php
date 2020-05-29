<?php

namespace App\Http\Controllers;

use App\Admin;
use Illuminate\Http\Request;
use App\Category;
use Illuminate\Support\Facades\DB;

use App\Parner;

class AdminController extends Controller
{

    public function __construct()
    {
        // @author: ledinhbinh
        $this->middleware('auth:admin');
        //
    }

    public function index()
    {
        // tien thu duoc hang thang
        $monthlyMonney          = $this->monthlyMonney();

        // ti le tien thang nay
        $ratioThisMonthMoney    = $this->ratioThisMonthMoney();

        // ngay hom nay ban duoc bao nhieu tien???
        $todayArrFormat = array(date('Y-m-d'));
        $moneyToday = DB::select('select sum(products.price) as total from products, product_user_transactions where products.id = product_user_transactions.product_id and date(product_user_transactions.created_at) = ?', $todayArrFormat)[0]->total;

        // ngay hom nay co bao nhieu giao dich doi diem???
        $exchangeToday = DB::select('select count(*) as total from parner_user_transactions where date(created_at) = ?', $todayArrFormat)[0]->total;

        $orderToday = DB::select('select count(*) as total from product_user_transactions where date(created_at) = ?', $todayArrFormat)[0]->total;

        $requestToday = DB::select('select count(*) as total from requests where date(created_at) = ?', $todayArrFormat)[0]->total;

        // dd($moneyToday);

        // gui data thong ke duoc ra dashboard
        return view('admins.admin', [
            'monthlyMonney'         => $monthlyMonney,
            'ratioThisMonthMoney'   => $ratioThisMonthMoney,
            'moneyToday'            => $moneyToday,
            'exchangeToday'         => $exchangeToday,
            'orderToday'            => $orderToday,
            'requestToday'          => $requestToday,
        ]);
    }

    public function showCreateProductForm()
    {
        $parners = Parner::all();
        return view('admins.product', [
            'parners' => $parners
        ]);
    }

    public function createProduct(Request $request)
    {
        $data = $this->validate($request, [
            'parner_name'   => 'required',
            'name'          => 'required',
            'image'         => 'required|image',
            'description'   => '',
            'price'         => 'numeric',
            'quantity'      => 'numeric',
            'bonus_point'   => 'numeric',
        ]);

        dd($data);

        Product::create($data);

        return redirect()->back->with('msg', 'create product successfully!');
    }

    public function monthlyMonney()
    {
        // thu tien tu ban san pham
        $moneyFromSelling = [100, 200, 300, 400, 500, 600];

        // thu tien tu trao doi diem
        $moneyFromDiscounting = [100, 200, 300, 400, 500, 600];

        // cong tong lai theo moi thang
            return array_map(function ($a, $b) {
                    return $a + $b;
                },
                $moneyFromSelling,
                $moneyFromDiscounting
            );

    }

    public function ratioThisMonthMoney()
    {
        return [30, 70];
    }
}
