<?php

namespace App\Http\Controllers;

use App\Http\Models\InquiryForm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Models\FtStaticPages;

class PagesController extends Controller
{
    /**
     * @param string $seoUrl
     * @return array
     */
    public function index(string $seoUrl): array
    {
        if (empty($seoUrl)) {
            return [];
        }
        $result = FtStaticPages::where('status', 1)->where('url', $seoUrl)->first();
        if (!$result) {
            return [];
        }
       // header('Access-Control-Allow-Origin: *');
        return [
            'id' => $result->id,
            'title' => $result->title,
            'seoUrl' => $result->url,
            'content' => $result->content,
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function references(): array
    {
        $FtStaticPages = new FtStaticPages();
        $result = $FtStaticPages->getReferencesList();
        //  echo "<pre>"; print_r($result);exit;
       // header('Access-Control-Allow-Origin: *');
        return array_map(
            function ($item) {
                return [
                    'no' => $item->id,
                    'organisationName' => $item->reference_name,
                    'country' => $item->country_name,
                    'country_name' => $item->country_name,
                ];
            },
            $result
        );
    }

    public function training()
    {
        $FtStaticPages = new FtStaticPages();
        $training_categories = $FtStaticPages->getTrainingCategories();
        $training = $FtStaticPages->getTraining();

        if (!empty($training_categories)) {
            foreach ($training_categories as &$training_category) {
                $training_category->scheduledCourses = $training[$training_category->categoryId] ?? [];
            }
            foreach ($training_categories as &$training_category) {
                $training_category->onDemandCourses = $training[$training_category->categoryId] ?? [];
            }
        }
  //      header('Access-Control-Allow-Origin: *');
        return $training_categories;
    }

    public function countries()
    {
//        header('Access-Control-Allow-Origin: *');
        $FtStaticPages = new FtStaticPages();
        return $FtStaticPages->getCountries();
    }

    public function trainingCategories()
    {
    //    header('Access-Control-Allow-Origin: *');
        $FtStaticPages = new FtStaticPages();
        return $FtStaticPages->getTrainingCategories();
    }

    public function setInstructorApplicationForm(Request $request): array
    {
        //header('Access-Control-Allow-Origin: *');
        $input = $request->all();
        echo "<pre>";
        print_r($input);
        return ['success' => true];
    }

    public function faqFrequentInquiries()
    {
        //header('Access-Control-Allow-Origin: *');
        $FtStaticPages = new FtStaticPages();
        return $FtStaticPages->getFaqFrequentInquiries();
    }

    /**
     * @param int $educationId
     * @return array
     */
    public function trainingsForm(int $educationId): array
    {
        if (empty($educationId)) {
            return [];
        }
        $FtStaticPages = new FtStaticPages();
        $result = $FtStaticPages->getTrainingsForm($educationId);
       // header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
        return array_map(
            function ($item) {
                return [
                    'educationId' => $item->education_id,
                    'educationName' => $item->education_name,
                    'educationTime' => $item->education_time,
                    'code' => $item->code,
                    'startingDate' => $item->starting_date,
                    'fee' => $item->fee,
                    'educationPlace' => $item->education_place,
                ];
            },
            $result
        );
    }
    public function setInquiryForm(Request $request)
    {
        //header('Access-Control-Allow-Origin: *');
        $params = json_decode($request->getContent(), true);
        try {
             $response = [
                'egitim_kategori' => $params['educationCategoryId'],
                'egitim_id' => $params['educationId'],
                'kayit_ip' => \Request::ip(),
                'unvan_id' => $params['salutation'],
                'adi_soyadi' => $params['name'],
               // 'referans_id' => $params['referenceId'],
                'sirket_adi' => $params['company'],
                'sirket_web' => $params['companyWebsite'],
                'isinin_adi' => $params['job'],
                'ulke_id' => $params['countryId'],
                'sehir_adi' => $params['city'],
                'cep_tel_kodu' => $params['phoneCode'],
                'cep_tel' => $params['phone'],
                'faks_kodu' => $params['faxCode'],
                'faks' => $params['fax'],
                'email' => $params['email'],
                'mesaj' => $params['inquiryComment'],
            ];
             $InquiryForm = new FtStaticPages();
            $InquiryForm->saveInquiryForm($response);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $errorResJson = $e
                ->getResponse()
                ->getBody()
                ->getContents();
            $errorRes = json_decode(stripslashes($errorResJson), true);
            // Return error
            return response()->json(
                [
                    'message' => 'error',
                ],
                $errorRes['response']['code']
            );
        }
        // Return success
        return response()->json(
            [
                'status' => '200',
                'message' => 'success'
            ],
            200
        );
    }

}
