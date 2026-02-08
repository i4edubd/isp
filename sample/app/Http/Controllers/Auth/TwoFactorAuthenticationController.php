<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;


class TwoFactorAuthenticationController extends Controller
{
    /**
     * Display the Two Factor Login Settings Status.
     *
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $operator = $request->user();
        return match ($operator->role) {
            'super_admin' => view('admins.super_admin.two-factor-status'),
            'group_admin' =>  view('admins.group_admin.two-factor-status'),
            'operator' => view('admins.operator.two-factor-status'),
            'sub_operator' => view('admins.sub_operator.two-factor-status'),
            'manager' =>   view('admins.manager.two-factor-status'),
            'developer' => view('admins.developer.two-factor-status'),
            'sales_manager' =>  view('admins.sales_manager.two-factor-status'),
            default => 'Not Found',
        };
    }

    /**
     * Display the Two Factor Login create form.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $google2fa = new Google2FA();

        $operator = $request->user();
        $operator->two_factor_secret =  $google2fa->generateSecretKey();
        $operator->two_factor_activated = 0;
        $operator->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            $operator->company,
            $operator->email,
            $operator->two_factor_secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $qrcode_image = base64_encode($writer->writeString($qrCodeUrl));

        return match ($operator->role) {
            'super_admin' => view('admins.super_admin.two-factor-create', [
                'qrcode_image' => $qrcode_image,
            ]),

            'group_admin' =>  view('admins.group_admin.two-factor-create', [
                'qrcode_image' => $qrcode_image,
            ]),

            'operator' => view('admins.operator.two-factor-create', [
                'qrcode_image' => $qrcode_image,
            ]),

            'sub_operator' => view('admins.sub_operator.two-factor-create', [
                'qrcode_image' => $qrcode_image,
            ]),

            'manager' =>   view('admins.manager.two-factor-create', [
                'qrcode_image' => $qrcode_image,
            ]),

            'developer' => view('admins.developer.two-factor-create', [
                'qrcode_image' => $qrcode_image,
            ]),

            'sales_manager' =>  view('admins.sales_manager.two-factor-create', [
                'qrcode_image' => $qrcode_image,
            ]),

            default => 'Not Found',
        };
    }

    /**
     * Handle the two factor login enable request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(Request $request)
    {
        $operator = $request->user();
        $google2fa = new Google2FA();
        $code = $request->input('code');
        $valid = $google2fa->verifyKey($operator->two_factor_secret, $code);
        if ($valid) {
            if ($operator->mgid != (int)config('consumer.demo_gid')) {
                $operator->two_factor_activated = 1;
                $operator->save();
            }
            return redirect()->route('two-factor.show')->with('success', 'Two Factor Authentication has been enabled successfully!');
        } else {
            return redirect()->route('two-factor.create')->with('info', 'Two Factor Code Verification Failed');
        }
    }

    /**
     * Handle the two factor login disable request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $operator = $request->user();
        $operator->two_factor_activated = 0;
        $operator->two_factor_secret =  null;
        $operator->save();
        return redirect()->route('two-factor.show')->with('success', 'Two Factor Authentication has been disabled successfully!');
    }
}
