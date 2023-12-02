<?php

namespace App\Http\Middleware;

use Closure;

class PMMailGonderim
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->konu == "")
            return redirect()->back()->withErrors('Please fill Subject');
        if($request->from_email == "")
            return redirect()->back()->withErrors('Please fill From Email (Reply To)');
        if($request->to_email == "")
            return redirect()->back()->withErrors('Please fill To (Training Department Contact Person)');
        if($request->icerik == "")
            return redirect()->back()->withErrors('Please fill Email Content');

        return $next($request);
    }
}
