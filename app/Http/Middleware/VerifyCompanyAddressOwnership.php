<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\CompanyAddress;

class VerifyCompanyAddressOwnership
{
    public function handle(Request $request, Closure $next)
    {
        $company = $request->route('company');
        $addressId = $request->route('id');

        if ($addressId) {
            $addressExists = CompanyAddress::where('id', $addressId)
                ->where('company_id', $company->id)
                ->exists();

            if (!$addressExists) {
                return response()->json([
                    'error' => 'The address does not belong to the specified company.'
                ], 404);
            }
        }

        return $next($request);
    }
}