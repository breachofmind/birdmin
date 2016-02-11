<?php

namespace Birdmin\Http\Controllers\Auth;

use Birdmin\Core\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Birdmin\Core\Template;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(Template $template)
    {
        parent::__construct($template);

        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * GET Login page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index ()
    {
        $this->template->bodyClass = "login";

        return view('cms::login');
    }

    /**
     * POST auth
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function authenticate(Request $request)
    {
        if ($errors = Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Authentication passed...
            return redirect()->intended(cms_url());
        }
        return $this->index()->withErrors([
            'User name or password was not correct. Please try again.'
        ]);
    }


    /**
     * GET logout
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        Auth::logout();

        return redirect(cms_url('login?landed=true'));
    }
}
