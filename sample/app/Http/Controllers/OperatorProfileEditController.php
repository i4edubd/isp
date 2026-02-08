<?php

namespace App\Http\Controllers;

use App\Models\card_distributor;
use App\Models\country;
use App\Models\language;
use App\Models\operator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class OperatorProfileEditController extends Controller
{
    /**
     * Return the Image Extension
     *
     * @param  string $mime
     * @return string
     */
    public function mime2ext($mime)
    {
        $mime_map = [
            'image/gif' => '.gif',
            'image/jpeg' => '.jpeg',
            'image/png' => '.png',
        ];
        return isset($mime_map[$mime]) === true ? $mime_map[$mime] : false;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function index(operator $operator)
    {
        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.operator-profile', [
                    'operator' => $operator,
                ]);
                break;

            case 'operator':
                return view('admins.operator.operator-profile', [
                    'operator' => $operator,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.operator-profile', [
                    'operator' => $operator,
                ]);
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(operator $operator)
    {
        // countries
        $countries = country::all();
        $country = $countries->firstWhere('id', '=', $operator->country_id);
        if ($country) {
            $countries =  $countries->prepend($country);
        }

        // languages
        $languages = language::all();
        $languages = $languages->prepend(getLanguage($operator));

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.operator-profile-edit', [
                    'operator' => $operator,
                    'countries' => $countries,
                    'languages' => $languages,
                ]);
                break;

            case 'operator':
                return view('admins.operator.operator-profile-edit', [
                    'operator' => $operator,
                    'countries' => $countries,
                    'languages' => $languages,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.operator-profile-edit', [
                    'operator' => $operator,
                    'countries' => $countries,
                    'languages' => $languages,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, operator $operator)
    {
        // << validate
        $request->validate([
            'country_id' => 'numeric|exists:countries,id|required',
            'lang_code' => 'string|exists:languages,code|required',
            'timezone' => 'string|exists:timezones,name|required',
        ]);

        $request->validate([
            'helpline' => 'string|max:254',
        ]);

        if ($request->user()->can('editCompany', $operator)) {
            $request->validate([
                'company' => 'required|string|max:254',
                'company_in_native_lang' => 'required|string|max:254',
            ]);
        }

        $country = country::findOrFail($request->country_id);

        $mobile = validate_mobile($request->mobile, $country->iso2);

        if ($mobile == 0) {
            return redirect()->route('operators.profile.create', ['operator' => $operator->id])->with('error', 'Invalid Mobile Number');
        }
        // validate >>

        if ($request->file('company_logo')) {

            $date = new \DateTime();

            $filename = $operator->id . $date->getTimestamp();

            $UploadedFile = Image::make($request->file('company_logo'));

            $mime = $UploadedFile->mime;

            if ($this->mime2ext($mime)) {
                $extension = $this->mime2ext($mime);
            } else {
                return redirect()->route('operators.profile.create', ['operator' => $operator->id])->with('error', 'Invalid Image!');
            }

            $image_name =  $filename . $extension;

            $image = 'public/' . $image_name;

            $path = Storage::path($image);

            $UploadedFile->resize(120, null,  function ($constraint) {
                $constraint->aspectRatio();
            })->save($path);
        } else {
            $image = 0;
        }

        if ($request->user()->can('editCompany', $operator)) {
            $operator->company = $request->company;
            $operator->company_in_native_lang = $request->company_in_native_lang;
        }

        $operator->country_id = $country->id;
        $operator->timezone = $request->timezone;
        $operator->lang_code = $request->lang_code;
        $operator->mobile = $mobile;
        $operator->helpline = $request->helpline;
        $operator->house_no = $request->house_no;
        $operator->road_no = $request->road_no;
        $operator->district = $request->district;
        $operator->postal_code = $request->postal_code;
        if ($image) {
            $operator->company_logo = $image_name;
        }
        $operator->save();

        if ($operator->wasChanged('country_id') || $operator->wasChanged('lang_code')) {

            $managers = operator::where('gid', $request->user()->id)->where('role', 'manager')->get();
            foreach ($managers as $manager) {
                $manager->country_id = $operator->country_id;
                $manager->timezone = $operator->timezone;
                $manager->lang_code = $operator->lang_code;
                $manager->save();
            }

            $card_distributors = card_distributor::where('operator_id', $request->user()->id)->get();
            foreach ($card_distributors as $card_distributor) {
                $card_distributor->country_id = $operator->country_id;
                $card_distributor->timezone = $operator->timezone;
                $card_distributor->lang_code = $operator->lang_code;
                $card_distributor->save();
            }
        }

        return redirect()->route('operators.profile.index', ['operator' => $operator->id])->with('success', 'Biller information updated successfully!');
    }
}
