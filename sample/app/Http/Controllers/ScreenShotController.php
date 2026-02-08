<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;
use Illuminate\Http\Request;

class ScreenShotController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        $request->validate([
            'page' => 'required',
            'sd' => 'numeric|nullable',
        ]);

        if (config('local.chromium_executable') == 'default') {
            $browserFactory = new BrowserFactory();
        } else {
            $browserFactory = new BrowserFactory(config('local.chromium_executable'));
        }

        // starts headless chrome
        $browser = $browserFactory->createBrowser([
            'headers'   => ['cookie' => $request->headers->get('cookie')],
            'enableImages' => true,
            'windowSize' => [1280, 1024],
        ]);

        try {
            // creates a new page and navigate to an URL
            $page = $browser->createPage();
            $page->navigate($request->page)->waitForNavigation(Page::NETWORK_IDLE);

            // scroll down
            if ($request->filled('sd')) {
                $page->mouse()->scrollDown($request->sd);
            }

            // find and click at an element with given id
            if ($request->filled('cid')) {
                $page->mouse()->find('#' . $request->cid, 1)->click();
            }

            // screenshot
            $path = storage_path('app/screenshots/');
            $file_name = 'screenshot' . Carbon::now()->timestamp . '.png';
            $img = $path . $file_name;
            $page->screenshot()->saveToFile($img);
        } finally {
            // bye
            $browser->close();
        }
    }
}
