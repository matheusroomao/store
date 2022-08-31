<?php

namespace App\Repository\Business;

use App\Models\Product;
use App\Models\UserProduct;
use App\Repository\Contracts\UserProductInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProductRepository extends AbstractRepository implements UserProductInterface
{
    private $model = UserProduct::class;
    private $relationships = ['product','user'];
    private $dependences = [];
    private $unique = [];
    private $message = null;
    private $order = 'name';
    private $upload = [];


    public function __construct()
    {
        $this->model = app($this->model);
        parent::__construct($this->model, $this->relationships, $this->dependences, $this->unique, $this->upload);
    }

    public function findPaginate(Request $request)
    {
        $models = $this->model->query()->with($this->relationships);

        if(auth()->user()->type != "ADMIN"){
            $models = $this->model->query()->with($this->relationships)->where('user_id',auth()->user()->id);

        }

        if ($request->exists('search')) {
            $this->setFilterGlobal($request, $models);
        } else {
            $this->setFilterByColumn($request, $models);
        }
        $this->setOrder($request, $models);
        $models = $models->paginate(8);
        $this->setMessage('Consulta Finalizada', 'success');
        return $models;
    }

    
    public function save(Request $request)
    {
        $model = new $this->model();
        $model->product_id = $request->product_id; 
        $model->status = "NOVO";
        $model->user_id = Auth::user()->id;
        $model->save();

        $this->setMessage('Compra efetuada com sucesso.', 'success');
        return $model;
    }
}

