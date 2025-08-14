<?php

namespace App\Http\Controllers\BackEnd\Location;

use App\Http\Controllers\Controller;
use App\Models\Car\CarContent;
use App\Models\Language;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class StateController extends Controller
{
    public function index(Request $request)
    {

        $language = Language::query()->where('code', '=', $request->language)->firstOrFail();
        $information['countries'] = $language->countryInfo()->orderByDesc('id')->get();
        $information['states'] = $language->stateInfo()->orderByDesc('id')->get();
        $information['langs'] = Language::all();
        $information['language'] = $language;

        return view('backend.location.state.index', $information);
    }
    public function getCountry($language_id)
    {
        $countries = Country::where('language_id', $language_id)->get();
        $states = State::where('language_id', $language_id)->get();

        return response()->json([
            'status' => 'success',
            'countries' => $countries,
            'states' => $states
        ], 200);
    }

    public function store(Request $request)
    {
        $totalCountry = Country::Where('language_id', $request->m_language_id)->count();
        if ($totalCountry > 0) {
            $country = true;
        } else {
            $country = false;
        }

        $rules = [
            'm_language_id' => 'required',
            'name' => 'required',
            'country_id' => $country ? 'required' : '',
        ];

        $messages = [
            'm_language_id.required' => __('The language field is required.'),
            'name.required' => __('The name field is required.')
        ];


        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $state = new State();

        // Use the correct field names here
        $state->language_id = $request->m_language_id;
        $state->name = $request->name;
        $state->slug = createSlug($request->name);
        $state->country_id = $request->country_id;
        $state->save();

        Session::flash('success', __('State stored successfully') . '!');

        return response()->json(['status' => 'success'], 200);
    }


    public function update(Request $request)
    {
        $rules = [
            'name' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $stateInfo = State::find($request->id);

        $data = $request->except('language');
        $data['slug'] = createSlug($request->name);

        $stateInfo->update($data);

        Session::flash('success', __('Country updated successfully') . '!');

        return Response::json(['status' => 'success'], 200);
    }

    public function destroy($id)
    {
        $State = State::query()->find($id);

        $city = City::Where('state_id', $id)->get();
        $car_content = CarContent::Where('state_id', $id)->get();

        if (count($city) > 0) {
            return redirect()->back()->with('warning', __('First delete all the city of this State') . '!');
        } else {

            if (count($car_content) > 0) {
                return redirect()->back()->with('warning', __('First delete all the car of this State') . '!');
            } else {

                $State->delete();
                return redirect()->back()->with('success', __('State deleted successfully') . '!');
            }
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request['ids'];

        $errorOccurred = false;
        $errorOccurred2 = false;
        foreach ($ids as $id) {
            $State = State::query()->find($id);
            $city = City::Where('state_id', $id)->get();
            $car_content = CarContent::Where('state_id', $id)->get();

            if (count($city) > 0) {

                $errorOccurred = true;
                break;
            } else {
                if (count($car_content) > 0) {
                    $errorOccurred2 = true;
                    break;
                } else {
                    $State->delete();
                }
            }
        }

        if ($errorOccurred == true) {
            Session::flash('warning', __('First delete all the city of these State') . '!');
        } elseif ($errorOccurred2 == true) {
            Session::flash('warning', __('First delete all the car of these State') . '!');
        } else {
            Session::flash('success', __('Selected Informations deleted successfully') . '!');
        }
        return Response::json(['status' => 'success'], 200);
    }
}
