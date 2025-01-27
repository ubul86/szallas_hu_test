<?php

namespace App\Http\Middleware;

use App\Models\CompanyEmployee;
use Closure;
use Illuminate\Http\Request;

class VerifyCompanyEmployeeOwnership
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var Company|null $company */
        $company = $request->route('company');
        $employeeId = $request->route('id');

        if (!$company) {
            return response()->json([
                'error' => 'Company not found.'
            ], 404);
        }

        if ($employeeId) {
            $employeeExists = CompanyEmployee::where('id', $employeeId)
                ->where('company_id', $company->id)
                ->exists();

            if (!$employeeExists) {
                return response()->json([
                    'error' => 'The employee does not belong to the specified company.'
                ], 404);
            }
        }

        return $next($request);
    }
}
