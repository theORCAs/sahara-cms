<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// model olusturma              php artisan make:model Http/Models/EgitimKayitlar -r
// controller oluşturma         php artisan make:controller SystemSetupController --resource

Route::post('/forgetPass', 'PasswordResetController@forgetPass');

Auth::routes();

Route::get('/', 'HomeController@index');
Route::post('/mail_gonder', 'SendEmailController@mailGonder');

Route::get('/switchUser', 'HomeController@switchUserModal');
Route::get('/endSwitchUser', 'HomeController@switchUserKapat');
Route::post('/su_kullaniciGetir', 'HomeController@su_kullanicilariGetir');
Route::post('/su_GecisYap', 'HomeController@su_GecisYap');


//Route::resource("egitim_kayitlar", 'EgitimKayitlarController');
// Route::get("/confirmed_courses", "HomeController@confirmed_courses");

Route::get("/convertToString/{number}", "HomeController@convertNumberToString");

// Route::resource('/if_view/{flg_durum}', 'EgitimArastirmaController', ['parameters' => ['{flg_durum}' => 'not_id']]);

// Registration & Proposal Module begin
Route::group([], function () {
// Confirmed Courses - All begin
    Route::group([], function () {
        Route::get('/participantPhoto/{teklif_id}', 'KatilimcilarFotoController@index');
        Route::post('/participantPhoto/{teklif_id}', 'KatilimcilarFotoController@store');
        Route::get('/participantPhoto/{foto_id}/edit', 'KatilimcilarFotoController@edit');
        Route::put('/participantPhoto/{foto_id}', 'KatilimcilarFotoController@update');
        Route::delete('/participantPhoto/{foto_id}', 'KatilimcilarFotoController@destroy');

        Route::get('/teklifFormlar/{teklif_id}/{tur_id}', 'TeklifFormlarController@index');
        Route::post('/teklifFormlar/{teklif_id}', 'TeklifFormlarController@store');
        Route::delete('/teklifFormlar/{form_id}', 'TeklifFormlarController@destroy');

        Route::get('/cc_now', 'TekliflerController@cc_now');
        Route::post('/cc_now/search', 'TekliflerController@search');
        //Route::get('cc_now/{filtre_yil?}/{filtre_ulke_id?}/{sirket_id?}', 'TekliflerController@cc_now');
        Route::get('/cc_past', 'TekliflerController@cc_past');

        Route::post('/cc_past/filtreUlkeGetirJSON', 'TekliflerController@filtreUlkeGetirJSON');
        Route::post('/cc_past/filtreSirketGetirJSON', 'TekliflerController@filtreSirketGetirJSON');
        Route::post('/cc_past/filtreHocaOdemeGetirJSON', 'TekliflerController@filtreHocaOdemeGetirJSON');
        Route::post('/cc_past/filtreKursOdemeGetirJSON', 'TekliflerController@filtreKursOdemeGetirJSON');

        Route::post('/cc_past/searchPast', 'TekliflerController@searchPast');
        Route::get('/{prefix}/courseAssignMailView/{egitim_hoca_id}', 'TekliflerController@courseAssignMailView');
        Route::post('/{prefix}/courseAssignMailSend/{egitim_hoca_id}', 'TekliflerController@courseAssignMailSend');
        Route::get('/{prefix}/coursePaymentMailView/{egitim_hoca_id}', 'TekliflerController@coursePaymentMailView');
        Route::post('/{prefix}/coursePaymentMailSend/{egitim_hoca_id}', 'TekliflerController@coursePaymentMailSend');
        Route::get('/{prefix}/insxsetup/{teklif_id}', 'TekliflerController@instructorXSetup');
        Route::get('/{prefix}/insxsetup/{teklif_id}/edit/{egitim_hoca_id}', 'TekliflerController@instructorXSetupEdit');
        Route::resource('/egitim_hocalar', 'EgitimHocalarController');
        Route::get('/{prefix}/assignTrainingLocationView/{teklif_id}', 'TekliflerController@assignTrainingLocationView');
        Route::get('/{prefix}/setAssignTrainingLocation/{kurs_yeri_id}/{teklif_id}', 'TekliflerController@setAssignTrainingLocation');
        Route::get('/{prefix}/unsetAssignTrainingLocation/{kurs_yeri_id}/{teklif_id}', 'TekliflerController@unsetAssignTrainingLocation');
        Route::get('/{prefix}/tLocationInstructorMailView/{teklif_id}', 'TekliflerController@tLocationInstructorMailView');
        Route::post('/{prefix}/tLocationInstructorMailSend/{teklif_id}', 'TekliflerController@tLocationInstructorMailSend');
        Route::get('/{prefix}/tLocationParticipantMailView/{teklif_id}', 'TekliflerController@tLocationParticipantMailView');
        Route::post('/{prefix}/tLocationParticipantMailSend/{teklif_id}', 'TekliflerController@tLocationParticipantMailSend');
        Route::get('/{prefix}/hrAdminMailView/{teklif_id}', 'TekliflerController@hrAdminMailView');
        Route::post('/{prefix}/hrAdminMailSend/{teklif_id}', 'TekliflerController@hrAdminMailSend');
        Route::get('/{prefix}/paxCertificateView/{katilimci_id}/{teklif_id}', 'TekliflerController@paxCertificateView');
        Route::get('/{prefix}/paxExperienceMailView/{katilimci_id}/{teklif_id}', 'TekliflerController@paxExperienceMailView');
        Route::post('/{prefix}/paxExperienceMailSend/{katilimci_id}/{teklif_id}', 'TekliflerController@paxExperienceMailSend');
        Route::get('/{prefix}/allPaxExperienceMailView/{teklif_id}', 'TekliflerController@allPaxExperienceMailView');
        Route::post('/{prefix}/allPaxExperienceMailSend/{teklif_id}', 'TekliflerController@allPaxExperienceMailSend');
        Route::get('/{prefix}/teklifOdemeDurumDegistirJson/{teklif_id}/{flg_odendi}', 'TekliflerController@teklifOdemeDurumDegistirJson');
        Route::get('/{prefix}/yorumYazModalView', 'TekliflerController@yorumYazModalView');
        Route::post('/{prefix}/yorumYazModalSendJson', 'TekliflerController@yorumYazModalSendJson');
        Route::get('/{prefix}/visaLetterReqMailView/{teklif_id}', 'TekliflerController@visaLetterReqMailView');
        Route::post('/{prefix}/visaLetterReqMailSend/{teklif_id}', 'TekliflerController@visaLetterReqMailSend');
        Route::get('/{prefix}/visaFormFilledView/{teklif_id}', 'TekliflerController@visaFormFilledView');
        Route::post('/{prefix}/visaFormFilledSave/{teklif_id}', 'TekliflerController@visaFormFilledSave');
        Route::get('/{prefix}/visaLetterPDFView/{teklif_id}', 'TekliflerController@visaLetterPDFView');
        Route::post('/{prefix}/visaLetterPDFCreate/{teklif_id}', 'TekliflerController@visaLetterPDFCreate');
        Route::get('/{prefix}/visaLetterPDFMailView/{teklif_id}', 'TekliflerController@visaLetterPDFMailView');
        Route::post('/{prefix}/visaLetterPDFMailSend/{teklif_id}', 'TekliflerController@visaLetterPDFMailSend');
        Route::get('/{prefix}/airportTransferMailView/{teklif_id}', 'TekliflerController@airportTransferMailView');
        Route::post('/{prefix}/airportTransferMailSend/{teklif_id}', 'TekliflerController@airportTransferMailSend');
        Route::get('/{prefix}/hotelReservationFormMailView/{teklif_id}', 'TekliflerController@hotelReservationFormMailView');
        Route::post('/{prefix}/hotelReservationFormMailSend/{teklif_id}', 'TekliflerController@hotelReservationFormMailSend');
        Route::get('/{prefix}/meetingRoomReservationView/{tarih}/{kurs_yeri_id?}', 'TekliflerController@meetingRoomReservationView');
        Route::post('/{prefix}/meetingRoomReservationSave/{id?}', 'TekliflerController@meetingRoomReservationSave');
        Route::post('/{prefix}/meetingRoomReservationBolgeGetirJson', 'TekliflerController@meetingRoomReservationBolgeGetirJson');
        Route::post('/{prefix}/meetingRoomReservationOtelGetirJson', 'TekliflerController@meetingRoomReservationOtelGetirJson');
        Route::post('/{prefix}/meetingRoomReservationDelJson', 'TekliflerController@meetingRoomReservationDelJson');


    });
    // Confirmed Courses - All end

    // Confirmed Courses-Brief begin
    Route::get('/ccb', 'TekliflerController@ccb_view');
    Route::post('/ccb/searchCCB', 'TekliflerController@searchCCB');
    // Confirmed Courses-Brief end

    // Confirmed Course-Statictics begin
    Route::post('/ccs', 'TekliflerController@ccs_view');
    Route::get('/ccs', 'TekliflerController@ccs_view');
    Route::post('/ccs/filtreUlkeGetirJSON', 'TekliflerController@ccsFiltreUlkeGetirJSON');
    Route::post('/ccs/filtreSirketGetirJSON', 'TekliflerController@ccsFiltreSirketGetirJSON');
    // Confirmed Course-Statictics end

    // Course Attendance & Evaluation begin
    Route::group([], function () {
        Route::get('/cae_upcoming', 'EgitimDegerlendirmeController@cae_upcoming');
        Route::get('/cae_past', 'EgitimDegerlendirmeController@cae_past');
        Route::get('/{prefix}/evaluationFormCreate/{katilimci_id}', 'EgitimDegerlendirmeController@evaluationFormCreate');
        Route::get('/{prefix}/evaluationMailView/{katilimci_id}', 'EgitimDegerlendirmeController@evaluationMailView');
        Route::post('/{prefix}/evaluationMailSend/{katilimci_id}', 'EgitimDegerlendirmeController@evaluationMailSend');

    });
    // Course Attendance & Evaluation end

    // Training Operation begin
    Route::group([], function () {
        Route::resource('/to_outline', 'EgitimlerController');
        Route::get('/{prefix}/{id}/outline_edit', 'EgitimlerController@outlineEdit');
        Route::post('/{prefix}/{id}/outline_edit_save', 'EgitimlerController@outlineEditSave');
        Route::get('/{prefix}/{id}/schedule_edit', 'EgitimlerController@scheduleEdit');
        Route::post('/to_outline/egitimTarihSaveJson/{id}', 'EgitimlerController@egitimTarihSaveJson');
        Route::post('/to_outline/egitimTarihDelJson/{id}', 'EgitimlerController@egitimTarihDelJson');
        Route::post('/{prefix}/{id}/schedule_edit_save_form1', 'EgitimlerController@egitimTarihiEkleYearly');
        Route::post('/{prefix}/{id}/schedule_edit_save_form2', 'EgitimlerController@egitimTarihiEkleIndividual');
        Route::get('/to_outline/outlinePdfCreate/{egitim_id}/{sch?}', 'EgitimlerController@outlinePdfCreate');
        Route::post('/to_outline/search', 'EgitimlerController@search');
        Route::resource('/to_categories', 'EgitimKategoriController');
        Route::post('/to_categories/changeCategoryOrder', 'EgitimKategoriController@changeCateogryOrder');
    });
    // Training Operation end

    // Inqury Form begin
    Route::group([], function () {
        Route::resource('/if_tobechecked', 'EgitimArastirmaController');
        Route::resource('/if_checked', 'EgitimArastirmaController');
        Route::get('/if_tobechecked', 'EgitimArastirmaController@toBeChecked');
        Route::get('/if_checked', 'EgitimArastirmaController@checked');
        Route::get('/if_tobechecked/sendEmailView/{id}', 'EgitimArastirmaController@sendEmailView');
        Route::post('/if_tobechecked/sendEmail', 'EgitimArastirmaController@sendEmail');
    });
    // Inqury Form end

    // Proposal Module begin
    Route::group([], function () {
        Route::resource('pm_wait', 'EgitimKayitlarController');
        Route::get('pm_wait', 'EgitimKayitlarController@pm_wait');
        Route::post('pm_wait/refSirketGetirJson', 'EgitimKayitlarController@referansSirketGetirJson');
        Route::get("{prefix}/inv_pdf/{id}", 'EgitimKayitlarController@inv_pdf');
        Route::get('pm_wait/send_email/{egitim_kayit_id}', 'EgitimKayitlarController@send_email')->middleware(\App\Http\Middleware\CheckPdf::class);
        Route::post('pm_wait/sendEmail', 'EgitimKayitlarController@mail_gonder')->middleware(\App\Http\Middleware\PMMailGonderim::class);
        Route::resource('/pm_send', 'EgitimKayitlarController');
        Route::get('pm_send', 'EgitimKayitlarController@pm_send');
        Route::get('pm_wait/commentView/{egitim_kayit_id}/{teklif_id}', 'EgitimKayitlarController@commentView');
        Route::post('pm_wait/commentSave', 'EgitimKayitlarController@commentSave');
        Route::get('pm_rejected', 'EgitimKayitlarController@pm_rejected');
        Route::get('pm_all', 'EgitimKayitlarController@pm_all');
        Route::get('pm_deleted', 'EgitimKayitlarController@pm_deleted');

        Route::post("{tmp}/inv_pdf/save/{idsi}", 'EgitimKayitlarController@inv_pdf_kaydet');
        Route::post("{tmp}/inv_pdf/inv_pdf_create/{id}", 'EgitimKayitlarController@inv_pdf_create');
        Route::get('{tmp}/cnf_pdf/{id}', 'EgitimKayitlarController@cnf_pdf');
        Route::post("{tmp}/cnf_pdf/save/{idsi}", 'EgitimKayitlarController@cnf_pdf_kaydet');
        Route::post('{tmp}/cnf_pdf/cnf_pdf_create/{id}', 'EgitimKayitlarController@cnf_pdf_create');
        Route::get('{tmp}/prp_pdf/{id}', 'EgitimKayitlarController@prp_pdf');
        Route::post("{tmp}/prp_pdf/save/{egitim_kayit_id}", 'EgitimKayitlarController@prp_pdf_kaydet');
        Route::post("{tmp}/prp_pdf/prp_pdf_create/{egitim_kayit_id}", 'EgitimKayitlarController@prp_pdf_create');
        Route::get('{tmp}/outl_pdf_create/{id}', 'EgitimKayitlarController@outl_pdf_create');

        Route::get('participant/view/{crf_id?}', 'KatilimcilarController@index');
        Route::put('participant/update/{crf_id}', 'KatilimcilarController@update');
        Route::get('participant/store/{crf_id}', 'KatilimcilarController@store');
        Route::delete('participant/delete/{katilim_id}', 'KatilimcilarController@destroy');
    });
    // Proposal Module end

    // Instructor Aplication Module begin
    Route::group([], function () {
        Route::resource('/ia_active', 'EgitmenlerController');
        Route::resource('/ia_request', 'EgitmenlerController');
        Route::resource('/ia_passive', 'EgitmenlerController');
        Route::resource('/ia_rejected', 'EgitmenlerController');
        Route::get('/ia_active', 'EgitmenlerController@aktif');
        Route::get('/ia_request', 'EgitmenlerController@bekleyen');
        Route::get('/{prefix}/personelEmailView/{egitmen_id}', 'EgitmenlerController@personelEmailView');
        Route::post('/personelEmailSend', 'EgitmenlerController@personelEmailSend');
        Route::get('/{prefix}/corporateEmailView/{egitmen_id}', 'EgitmenlerController@corporateEmailView');
        Route::get('/ia_evaluation', 'EgitmenlerController@egitmen_degerlendirme');
        Route::post('/ia_evaluation', 'EgitmenlerController@egitmen_degerlendirme');
        Route::get('/ia_utilized', 'EgitmenlerController@atanmis');
        Route::post('/ia_utilized/filtreUlkeGetirJSON', 'EgitmenlerController@filtreUlkeGetirJSON');
        Route::post('/ia_utilized/filtreDilGetirJSON', 'EgitmenlerController@filtreDilGetirJSON');
        Route::post('/ia_utilized/filtreEgitimKategoriGetirJSON', 'EgitmenlerController@filtreEgitimKategoriGetirJSON');
        Route::post('/ia_utilized/filtreEgitimGetirJSON', 'EgitmenlerController@filtreEgitimGetirJSON');
        Route::post('/ia_utilized/filtreHocaAdiGetirJson', 'EgitmenlerController@filtreHocaAdiGetirJson');
        Route::post('/{prefix}/ec_search', 'EgitmenlerController@search');
        Route::get('/ia_nonutilized', 'EgitmenlerController@atanmamis');
        Route::get('/ia_payment', 'EgitimHocalarController@egitmen_odemeler_listesi');
        Route::get('/ia_payment/setPayment/{id}', 'EgitimHocalarController@egitmenOdemeYap');
        Route::get('/ia_payment/delPayment/{id}', 'EgitimHocalarController@egitmenOdemeSil');
        Route::get('/ia_passive', 'EgitmenlerController@pasif');
        Route::get('/ia_rejected', 'EgitmenlerController@reddedilmis');
    });
    // Instructor Aplication Module end

});
// Registration & Proposal Module end

// Instructor Module begin
Route::group([], function () {
    // Instructor ASSIGNMENT Preference begin
    Route::group([], function () {
        Route::get('/iap_future', 'TekliflerController@kursuVerecekHocaTercihi');
        Route::get('/iap_past', 'TekliflerController@kursuVerecekHocaTercihiGecmis');
        Route::get('/{prefix}/cancelEmailStart/{teklif_id}/{kayitlar_ids}', 'TekliflerController@kursIptalMailGonderim');
        Route::post('/{prefix}/cancelEmailStartSend', 'TekliflerController@kursIptalMailGonderimSend');
        Route::get('/{prefix}/iasMail/{id}/{teklif_id}', 'TekliflerController@kursMailGonderim');
        Route::post('/iasMailSend', 'TekliflerController@kursMailGonderimSend');
    });
    // Instructor ASSIGNMENT Preference end

    Route::get('/ibcs_view', 'EgitmenBackgroundController@egitmenBackgroundKursSecim');
    Route::post('/ibcs_view/search', 'EgitmenBackgroundController@egitmenBackgroundKursSecim');
    Route::post('/ibcs_view/listeGetir', 'EgitmenBackgroundController@egitmenBackgroundListeGetir');

    Route::resource('/cv_view', 'EgitmenlerController');
    Route::post('/cv_view/cvDosyaSil', 'EgitmenlerController@cvDosyaSil');
    Route::post('/cv_view/he_sil', 'EgitmenlerController@hocaEgitimSil');
    Route::post('/cv_view/hcy_sil', 'EgitmenlerController@calistigiYerSil');
    Route::post('/cv_view/hak_sil', 'EgitmenlerController@aldigiKursSil');
    Route::resource('/cds_view', 'EgitmenKursTalipController');
    Route::post('/cds_secimYap', 'EgitmenKursTalipController@secimYap');

    Route::resource('/cm_view', 'EgitimMateryalController');
    Route::get('/cm_readandconfirm/{id}', 'EgitimMateryalController@readAndConfirmation');
    Route::post('/cm_readandconfirm_set', 'EgitimMateryalController@readAndConfirmationSet');
    Route::get('/cm_upload/{teklif_id}', 'EgitimMateryalController@upload');
    Route::post('/cm_upload_set', 'EgitimMateryalController@uploadYap');
    Route::post('/cm_upload_del/{id}', 'EgitimMateryalController@uploadDelete');

    Route::resource('/bsnc_view', 'EgitmenBackgroundController');
    Route::post('/bsnc_view/kategoriGetirJson', 'EgitmenBackgroundController@egitimKategoriGetirJson');
    Route::post('/bsnc_view/egitimOylamaYap', 'EgitmenBackgroundController@egitimOylamaYap');

    Route::get('/cosp_view', 'EgitmenlerController@cosp_onerdigiKurslar');

    Route::resource('/wtpnc_view', 'EgitmenKursOneriController');
    Route::get('/wtpnc_view', 'EgitmenKursOneriController@egitmenYeniKurs');

    // Outlines Suggested (by you) begin
    Route::group([], function () {
        Route::resource('/osnp_view', 'EgitmenKursOneriController');
        Route::get('/osnp_view', 'EgitmenKursOneriController@yoneticiYeniKurs');
        Route::resource('/osem_view', 'EgitmenKursOneriController');
        Route::get('/osem_view', 'EgitmenKursOneriController@yoneticiEditedKurs');
        Route::resource('/osap_view', 'EgitmenKursOneriController');
        Route::get('/osap_view', 'EgitmenKursOneriController@yoneticiKabulKurs');
        Route::resource('/ospu_view', 'EgitmenKursOneriController');
        Route::get('/ospu_view', 'EgitmenKursOneriController@yoneticiPasifKurs');
    });
    // Outlines Suggested (by you) end
});
// Instructor Module end

// Administrative Operations begin
Route::group([], function () {
    // Web Users begin
    Route::group([], function () {
        Route::resource('user_type', 'RollerController');
        Route::resource('active_user', 'KullanicilarController');
        Route::get('active_user', 'KullanicilarController@viewActiveUser');
        Route::resource('passive_user', 'KullanicilarController');
        Route::get('passive_user', 'KullanicilarController@viewPassiveUser');
        Route::post('{prefix}/kullanicilarGetirJson', 'KullanicilarController@kullanicilarGetirJson');
        Route::post('{prefix}/userSearch', 'KullanicilarController@userSearch');
        Route::post('authorization/yapiListesi', 'YetkilerController@yapiListesiView');
        Route::post('authorization/yapiEkle', 'YetkilerController@yapiEkleModal');
        Route::post('authorization/yetkiliListesi', 'YetkilerController@yetkiliListesiView');
        Route::resource('authorization', 'YetkilerController');
        Route::resource('yapi', 'YapiController');
        Route::get('/user_type/yetki/{id}', 'RollerController@yetki');
    });
    // Web Users end

    // Account Module (Admin) begin
    Route::group([], function () {
        Route::resource('/aca_spent', 'KasaController');
        Route::post('/aca_spent/search', "KasaController@search");
        Route::resource('/aca_received', 'KasaReceiveController');
        Route::resource('/aca_expensetype', 'GiderKalemleriController');
        Route::resource('/aca_toincome', 'GelirKalemleriController');
    });
    // Account Module (Admin) end

    // Email Module begin
    Route::group([], function () {
        Route::resource('/em_messagetemplate', 'EbultenTemplateController');
        Route::post('/em_messagetemplate/search', 'EbultenTemplateController@search');
        Route::resource('/em_sendemail', 'EbultenGonderimController');
        Route::post('/em_sendemail/sablonGetirJson', 'EbultenGonderimController@sablonGetirJson');
        Route::get('/em_sendemail/startsend/{id}', 'EbultenGonderimController@startSend');
        Route::resource('/em_emailgroup', 'EbultenGruplarController');
        Route::get('/em_grouplist/search/{grup_id?}', 'EbultenKayitlarController@search');
        Route::resource('/em_grouplist', 'EbultenKayitlarController');
        Route::post('/em_grouplist/refSirketListeJson', 'EbultenKayitlarController@refSirketListeJson');
        Route::get('/em_unsubscribedlist/{grup_id?}', 'EbultenCikanKayitlarController@index');
    });
    // Email Module end
});
// Administrative Operations end


// Office Management Module begin
Route::group([], function () {
    //Suppliers & Payment Module begin
    Route::group([], function () {
        Route::resource('/spm_watingpayment', 'OdemelerController');
        Route::get('/spm_watingpayment', 'OdemelerController@bekleyen');
        Route::post('/spm_watingpayment/partnerGetirJson', 'OdemelerController@partnerGetirJson');
        Route::resource('/spm_completedpayment', 'OdemelerController');
        Route::get('/spm_completedpayment', 'OdemelerController@odenmisler');
        Route::resource('/spm_customerdetail', 'PartnerlerController');
        Route::post('/spm_customerdetail/search', 'PartnerlerController@search');
        Route::resource('/spm_kategori', 'PartnerKategorileriController');
    });
    //Suppliers & Payment Module end

    //Job Follow-up Module begin
    Route::group([], function () {
        Route::resource('/jfu_waiting', 'ITIslerController');
        Route::get('/jfu_waiting', 'ITIslerController@bekleyen');
        Route::post('/jfu_waiting/isTurleriGetirJson', 'ITIslerController@isTurleriGetirJson');
        Route::post('/jfu_waiting/sirketListeGetirJson', 'ITIslerController@sirketListeGetirJson');
        Route::resource('/jfu_completed', 'ITIslerController');
        Route::get('/jfu_completed', 'ITIslerController@tamamlanmis');
        Route::resource('/jfu_jobtypes', 'ITIsTurleriController');
        Route::resource('/jfu_frequency', 'ITTekrarTurleriController');
        Route::resource('/jfu_category', 'ITKategorilerController');
    });
    //Job Follow-up Module end

    //Job Assigned to Me begin
    Route::group([], function () {
        Route::resource('/jatm_completed', 'ITIslerAtanmisController');
        Route::resource('/jatm_waiting', 'ITIslerAtanmisController');
        Route::get('/jatm_waiting', 'ITIslerAtanmisController@bekleyenAtanmis');
        Route::get('/jatm_completed', 'ITIslerAtanmisController@tamamlanmisAtanmis');
    });
    //Job Assigned to Me end

    //Stock Module begin
    Route::group([], function () {
        Route::resource('/sm_view', 'StokUrunlerController');
        Route::get('/sm_view/stockEntry/{id}', 'StokUrunlerController@stokGiris');
        Route::post('/sm_view/stockEntrySave', 'StokUrunlerController@stokGirisKaydet');
        Route::get('/sm_view/stockSDList/{id}', 'StokUrunlerController@stokListesi');
        Route::delete('/sm_view/stockSDListDel/{id}', 'StokUrunlerController@stokListesiDelete');
        Route::get('/sm_egitimliste', 'StokUrunlerController@egitimListe');
    });
    //Stock Module end
    //Check List Module begin
    Route::group([], function () {
        Route::resource('/chkl_list', 'CheckListController');
        Route::post('/chkl_list/search', 'CheckListController@search');
        Route::resource('/chkl_kategori', 'CheckListKategoriController');
    });
    //Check List Module end
    Route::get('/amp_view', 'KasaController@personelGiderListe');
});
// Office Management Module end

// Guest (Hotel, Transfer, Visa) begin
Route::group([], function () {
    // Hotel Registiration Module begin
    Route::group([], function () {
        Route::resource('/hrm_list', "OtellerController");
        Route::post('/hrm_list/search', "OtellerController@search");
        Route::post('/hrm_list/bolgeGetirJson', "OtellerController@bolgeleriGetirJson");
        Route::post('/hrm_list/dereceGetirJson', "OtellerController@dereceGetirJson");
        Route::resource('/hrm_region', "OtelBolgeleriController");
        Route::resource('/hrm_star', "OtelDereceController");
        Route::resource('/hrm_roomtype', "OtelOdaTipleriController");
        Route::resource('/hrm_viewoption', "OtelManzaralariController");
        Route::resource('/hrm_hotelcity', "OtelSehirleriController");
    });
    // Hotel Registiration Module end

    // Hotel Rezervation Module begin
    Route::group([], function () {
        Route::resource('/hrsm_request', 'OtelRezervasyonController');
        Route::get('/hrsm_request', "OtelRezervasyonController@bekleyenListe");
        Route::get('/{prefix}/teyitMaili/{id}', 'OtelRezervasyonController@teyitMaili');
        Route::get('/{prefix}/emailToHotel/{rezervasyon_oda_id}', 'OtelRezervasyonController@emailToHotel');
        Route::post('/{prefix}/emailToHotelSendMail/{rezervasyon_oda_id}', 'OtelRezervasyonController@emailToHotelSendMail');
        Route::get('/hrsm_processing', "OtelRezervasyonController@islemdekiListe");
        Route::get('/hrsm_confirmed', "OtelRezervasyonController@onayliListe");
    });
    // Hotel Rezervation Module end

    // Airpot Transfer begin
    Route::group([], function () {
        Route::resource('/at_confirmed_arr', 'HavaalaniTransferController');
        Route::get('/at_confirmed_arr', 'HavaalaniTransferController@onayliListe');
        Route::get('/{prefix}/airportsign/{id}', 'HavaalaniTransferController@airportSign');
        Route::get('/{prefix}/gtfOnayla/{id}', 'HavaalaniTransferController@gelisTransferFirmaOnayla');
        Route::get('/{prefix}/gtfOnaylama/{id}', 'HavaalaniTransferController@gelisTransferFirmaOnaylama');

        Route::get('/at_confirmed_dep', 'HavaalaniTransferController@onayliDepartureListe');

        Route::resource('/at_past', 'HavaalaniTransferController');
        Route::get('/at_past', 'HavaalaniTransferController@onayliGecmisListe');

        Route::resource('/at_airlineentry', 'HavayoluSirketController');
        Route::resource('/at_airport', 'HavaalanlariController');

        Route::resource('/at_rejected', 'HavaalaniTransferController');
        Route::get('/at_rejected', 'HavaalaniTransferController@reddedilmisListe');


    });
    // Airpot Transfer end
});

// Guest (Hotel, Transfer, Visa) end

// Website Material & Operations begin
Route::group([], function () {
    Route::resource('/ws_menuler', 'WSMenulerController');
    Route::group([], function () {
        Route::resource('/rcm_sectors', 'SektorlerController');
        Route::get('/rcm_contactlist/create', 'ReferanslarController@create');
        Route::get('/rcm_contactlist/{ulke_id?}', 'ReferanslarController@listedeDegil');
        Route::resource('/rcm_contactlist', 'ReferanslarController');

        Route::get('/rcm_referancelist/create', 'ReferanslarController@create');
        Route::get('/rcm_referancelist/{ulke_id?}', 'ReferanslarController@listede');
        Route::resource('/rcm_referancelist', 'ReferanslarController');
    });
    Route::resource('/faq_view', 'SssController');
    Route::resource('/ws_hppictures', 'WSAnasayfaResimlerController');

});
// Website Material & Operations end

// parameters and units begin
Route::group([''], function () {
    Route::resource('/countries_view', 'UlkelerController');
    Route::resource('/unsubscribe_reasons', 'EbultenCikisNedenleriController');
    Route::resource('/pu_csc_view', 'IletisimTurleriKategorileriController');
    Route::resource('/pu_cs_view', 'IletisimTurleriController');
    Route::resource('/ss_view', 'SystemSetupController');
    Route::resource('/to_location', 'EgitimYerleriController');
    Route::resource('form_setup', 'EmailSablonController');
    Route::resource('/currency_type_view', 'ParaBirimiController');
});
Route::get('/pages/{seoUrl}', [\App\Http\Controllers\PagesController::class, 'index']);
Route::get('/references', [\App\Http\Controllers\PagesController::class, 'references']);
Route::get('/training', [\App\Http\Controllers\PagesController::class, 'training']);
Route::get('/countries', [\App\Http\Controllers\PagesController::class, 'countries']);
Route::get('/training-categories', [\App\Http\Controllers\PagesController::class, 'trainingCategories']);
Route::get('/faq-frequent-inquiries', [\App\Http\Controllers\PagesController::class, 'faqFrequentInquiries']);
Route::match(array('GET','POST'),'/set-instructor-application-form', [\App\Http\Controllers\PagesController::class, 'setInstructorApplicationForm']);
Route::get('/trainingsForm/{categoryId}', [\App\Http\Controllers\PagesController::class, 'trainingsForm']);

// parameters and units end
