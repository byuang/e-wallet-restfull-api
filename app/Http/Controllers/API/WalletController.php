<?php

namespace App\Http\Controllers\API;
use App\Traits\Response;
use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    use Response;
    protected $walletService;

    public function __construct(WalletService $walletService) {
       $this->walletService = $walletService;
    }

    public function detail(){
        $wallet_id = auth()->user()->wallet?->id;
        if (empty($wallet_id)) {
            return $this->errorResponse(null, 'Wallet ID not found', 404);
        }

        $detail = $this->walletService->getDetail($wallet_id);
        if (empty($detail)) {
            return $this->errorResponse(null, 'Failed to retrieve wallet detail', 500);
        }

        $detail = $this->walletService->getDetail($wallet_id);
        return $this->successResponse( $detail, 'Successfully Get Detail Wallet');
    }

    public function topup(Request $request){
        $validator = Validator::make($request->all(),[
             'amount' => 'numeric|min:10000|required',
        ]);

        if($validator->fails()) {
            return $this->errorResponse(null, $validator->errors(), 422);
        }
        $topup = $this->walletService->topup($request);
        return $this->successResponse( $topup, 'Successfully Topup Wallet');
    }

    public function transfer(Request $request){
        $validator = Validator::make($request->all(),[
            'amount' => 'numeric|min:10000|required',
            'wallet_id' => 'int|required',
        ]);

        if($validator->fails()) {
            return $this->errorResponse(null, $validator->errors(), 422);
        }

        $transfer = $this->walletService->transfer($request);

        return $this->successResponse($transfer,'Successfully transfer balance');
    }

    public function withdraw(Request $request){
        $validator = Validator::make($request->all(),[
            'amount' => 'numeric|min:0|required',
        ]);

        if($validator->fails()) {
            return $this->errorResponse(null, $validator->errors(), 422);
        }

        $withdraw = $this->walletService->withdraw($request);

        return $this->successResponse($withdraw,'Successfully Widtrawed');
    }
}
