<?php

namespace App\Http\Middleware;

use App\Models\CompanyOwner;
use Closure;
use Illuminate\Http\Request;

class VerifyCompanyOwnerOwnership
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var Company|null $company */
        $company = $request->route('company');
        $ownerId = $request->route('id');

        if (!$company) {
            return response()->json([
                'error' => 'Company not found.'
            ], 404);
        }

        if ($ownerId) {
            $ownerExists = CompanyOwner::where('id', $ownerId)
                ->where('company_id', $company->id)
                ->exists();

            if (!$ownerExists) {
                return response()->json([
                    'error' => 'The owner does not belong to the specified company.'
                ], 404);
            }
        }

        return $next($request);
    }
}
