<?php

use App\Models\Accounting\MonthlyClosure;
use App\Models\AccountingCompany;
use App\Models\CatPaymentSchedule;
use App\Models\CatSupplierSpecialty;
use App\Models\DmiabaSupplierRegistration;
use App\Models\DmiAccgInterimPayments;
use App\Models\DmiBucketSignature;
use App\Models\DmiControlPlazaSustitution;
use App\Models\DmiEAccounting;
use App\Models\DmiOverviewActivities;
use App\Models\DmiRh\DmirhVacation;
use App\Models\DmiRhPaymentsLogs;
use App\Models\FoodMenu;
use App\Models\PersonalIntelisis;
use App\Models\SegUsuario;
use App\Models\SupplierSpecialty;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use GuzzleHttp\Client;

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


//Test
Route::get('test/eladio','TestEladioController@index');
Route::get('test/auto','Procore\Proveedores\VendorsController@me');
Route::get('test/RefreshToken','Procore\Proveedores\VendorsController@RefreshToken');
Route::get('getInfo','Procore\Proveedores\VendorsController@getInfoToken');
Route::get('redirect','Procore\Proveedores\VendorsController@callback');
// Route::get('correo','Procore\Proveedores\VendorsController@InviteVendor');
// Route::get('biotime','Alfa\RecursosHumanos\ReporteAsistenciaController@AddUsersBioTime');

Auth::routes();


Route::get('test/', function () {

	$data = DB::connection('erp_sqlsrv')->select("SET NOCOUNT ON; EXEC Intranet.dbo.spNominasPersonal D0020325, 2023");

	return $data;

});

Route::get('test-pdf/{id}', [App\Http\Controllers\Alfa\RecursosHumanos\WorkPermitsController::class, 'generarPDFWorkPermit']);
Route::get('test-pdf/vacation/{id}', [App\Http\Controllers\Alfa\RecursosHumanos\VacationController::class, 'generarPDFRecord']);
Route::get('test-email', [App\Http\Controllers\Alfa\Dining\DiningController::class, 'test']);

Route::get('/token', function () {
	return csrf_token();
});


Route::get('prueba', 'Auth\LoginController@prueba');
Route::get('test-payroll', 'ToolsController@testPayroll');
Route::post('login', 'Auth\LoginController@login');
// Route::get('registration', 'Auth\AuthController@registration');
// Route::post('post-registration', 'Auth\AuthController@postRegistration');
Route::get('index', 'InicioController@index')->name('index');
Route::get('ldap', 'ldapController@index');
Route::get('check', 'Auth\LoginController@check');
Route::get('check_Permisos', 'Auth\LoginController@check_Permisos');
Route::get('logout', 'Auth\LoginController@logout');

Route::prefix('/user')->group(function() {
	Route::get('/check-token/{id}', 'ApiLoginController@checkToken');
	Route::post('/login', 'ApiLoginController@login');
});

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/example', 'HomeController@example')->name('example');
// Route::get('login', 'Auth\AuthController@ShowLogin')->name('login');
// Route::get('logout', 'Auth\AuthController@logout')->name('logout');

/************  POST peticiones  *********************/
Route::post('post-login', 'Auth\AuthController@postLogin')->name('postlogin');

Route::post('store_file', 'FileUploadController@fileStore');
Route::post('crear_carpeta', 'FileUploadController@Crear_Carpeta');
Route::post('deletefile', 'FileUploadController@deleteFile');
Route::post('deleteCarpeta', 'FileUploadController@deleteCarpeta');
Route::post('download', 'FileUploadController@downloadFile');
Route::post('viewarchivo', 'FileUploadController@viewFile');
Route::post('addFavorito', 'FileUploadController@addFavorito');
Route::post('editarArchivo', 'FileUploadController@EditarArchivo');
Route::post('editarCarpeta', 'FileUploadController@EditarCarpeta');
Route::post('getCarpetas', 'DocumentosController@ControlArchivosPrivacidad');
Route::post('getSubCarpetas', 'DocumentosController@getSubCarpetas');
Route::post('segAuditoria', 'AuditoriaController@segAuditoria');
Route::post('generarReporteDescargas', 'AuditoriaController@GenerarReporteDescargas');
Route::post('generarReporteConsultas', 'AuditoriaController@GenerarReporteConsultas');
Route::post('generarReporteBusquedas', 'AuditoriaController@GenerarReporteBusquedas');
Route::post('getContadoresDocumentos', 'DocumentosController@contadoresDocumentos');

Route::post('getCarpetaspublic', 'DocumentosPublicosController@getCarpetas');
Route::post('getSubCarpetaspublic', 'DocumentosPublicosController@getSubCarpetas');
Route::post('getArchivos', 'DocumentosController@getArchivos');
Route::post('buscarArchivos', 'DocumentosController@buscador');
Route::post('buscarArchivospublicos', 'DocumentosPublicosController@buscadorPublico');
Route::post('getArchivosMasDescargados', 'DocumentosPublicosController@getArchivosMasDescargados');
Route::post('getArchivosMasVisitados', 'DocumentosPublicosController@getArchivosMasVisitados');
Route::post('getArchivosMasBuscados', 'DocumentosPublicosController@getArchivosMasBuscados');
Route::post('getArchivosMasFavoritos', 'DocumentosPublicosController@getArchivosMasFavoritos');
Route::post('updateTopico', 'DocumentosController@updateTopico');
Route::post('addTopico', 'DocumentosController@addTopico');
Route::post('updateTipoDoc', 'DocumentosController@updateTipoDoc');
Route::post('addTipoDoc', 'DocumentosController@addTipoDoc');
Route::post('updateArea', 'DocumentosController@updateArea');
Route::post('addArea', 'DocumentosController@addArea');
Route::post('updateModulo', 'DocumentosController@updateModulo');
Route::post('addModulo', 'DocumentosController@addModulo');
Route::post('updatePrivacidad', 'DocumentosController@updatePrivacidad');
Route::post('addPrivacidad', 'DocumentosController@addPrivacidad');
Route::post('getArchivosRecientes', 'DocumentosController@getArchivosRecientes');
Route::post('addUsuario', 'UsuariosController@addUsuario');
Route::post('EditarUsuario', 'UsuariosController@EditarUsuario');
Route::post('EditarTipoPermiso', 'UsuariosController@EditarTipoPermiso');
Route::post('addTipoPermiso', 'UsuariosController@addTipoPermiso');
Route::post('getSubseccionesUsuario', 'UsuariosController@getSubseccionesUsuario');

/**GET Informacion Peticiones de informacion  */
Route::get('getCatPrivacidadDocumento', 'DocumentosController@getCatPrivacidadDocumento');
Route::get('getCatTipoDocumento', 'DocumentosController@getCatTipoDocumento');
Route::get('getCatArea', 'DocumentosController@getCatArea');
Route::get('getCatModulo', 'DocumentosController@getCatModulo');
Route::get('getCatUbicacion', 'DocumentosController@getCatUbicacion');
Route::get('getCatTopico', 'DocumentosController@getCatTopico');
Route::get('getCatDocPadre', 'DocumentosController@getCatDocPadre');
Route::get('getCmbArchivos', 'DocumentosController@getCmbArchivos');
Route::get('getCatTipoAdjunto', 'DocumentosController@getCatTipoAdjunto');
Route::get('/prov', 'Proveedores@consulta_proveedores')->name('prov');
Route::get('getUsuarios', 'UsuariosController@getUsuarios');
Route::get('getUsuariosEmail', 'UsuariosController@getUsuariosEmail');
Route::get('getSubsecciones', 'UsuariosController@getSubsecciones');
Route::get('getSeccionesPermisos', 'UsuariosController@getSeccionesPermisos');
Route::get('getSecciones', 'UsuariosController@getSecciones');
Route::get('getTipoPermisos', 'UsuariosController@getTipoPermisos');
Route::get('getTipoPermisosAll', 'UsuariosController@getTipoPermisosAll');
Route::get('getTitulosDocumentos', 'AuditoriaController@getTitulosDocumentosReporte');
Route::get('NotificationCenter', 'NotificationCenterController@getNotificationsUser');
Route::post('changeStatusNotification', 'NotificationCenterController@changeStatusNotification');
Route::get('countNotification', 'NotificationCenterController@countNotification');



/** Rutas de cotizador DMI */
Route::group(['prefix' => 'project'], function () {
    Route::get('/','Dmicotiza\ProjectController@index')->name('project');
    Route::get('/{id}','Dmicotiza\ProjectController@show');
});

Route::group(['prefix' => 'department'], function () {
    Route::get('/{project}', 'Dmicotiza\DepartmentController@index')->name('department');
    Route::post('/store', 'Dmicotiza\DepartmentController@store')->name('department.store');
    Route::get('/show/{id}','Dmicotiza\DepartmentController@show')->name('department.show');
});
Route::get('getClassification','Dmicotiza\DepartmentController@getClassification')->name('classifications');
Route::get('destroyAmenity/{id}', 'Dmicotiza\DepartmentController@destroyAmenity');
Route::post('updateAmenity', 'Dmicotiza\DepartmentController@updateAmenity');
Route::post('updateDeparment', 'Dmicotiza\DepartmentController@update');
Route::post('storeStagePrice', 'Dmicotiza\StagePriceController@store');
Route::get('stagePrice/{id}', 'Dmicotiza\StagePriceController@index');
Route::get('destroyDeparment/{id}', 'Dmicotiza\DepartmentController@destroy');
Route::get('destroyStagePrice/{id}', 'Dmicotiza\StagePriceController@destroy');
Route::post('statusDeparment', 'Dmicotiza\DepartmentController@statusDeparment');
Route::get('getType/{idClassification}', 'Dmicotiza\DepartmentController@getType');
Route::get('getViewProject/{idView}', 'Dmicotiza\ViewController@show');
Route::post('storeProject', 'Dmicotiza\ProjectController@store');
Route::post('storeViewProject', 'Dmicotiza\ViewController@store');
Route::get('getCountStage/{idClassification}/{idProject}', 'Dmicotiza\StageController@stage');
Route::post('storeStage', 'Dmicotiza\StageController@store');
Route::post('storeSubdivision', 'Dmicotiza\SubdivisionController@store');


/** Rutas de DMIHD */
Route::get('getLocations', 'Dmihd\LocationController@index');
Route::post('storeLocation', 'Dmihd\LocationController@store');
Route::get('destroyLocation/{id}', 'Dmihd\LocationController@destroy');
Route::post('updateLocation', 'Dmihd\LocationController@edit');

Route::get('getAreas', 'Dmihd\AreaController@index');
Route::post('storeArea', 'Dmihd\AreaController@store');
Route::get('destroyArea/{id}', 'Dmihd\AreaController@destroy');
Route::post('editArea ', 'Dmihd\AreaController@edit');
Route::get('getAreaLocation/{id}','Dmihd\AreaController@getAreaLocation');

Route::get('getSubAreas', 'Dmihd\SubAreaController@index');
Route::post('storeSubArea', 'Dmihd\SubAreaController@store');
Route::get('destroySubArea/{id}', 'Dmihd\SubAreaController@destroy');
Route::post('editSubArea ', 'Dmihd\SubAreaController@edit');
Route::get('getSubAreaArea/{id}', 'Dmihd\SubAreaController@getSubAreaArea');

Route::get('getPriority', 'Dmihd\PriorityController@index');
Route::post('storePriority', 'Dmihd\PriorityController@store');
Route::get('showPriority/{id}', 'Dmihd\PriorityController@show');
Route::post('editPriority', 'Dmihd\PriorityController@edit');
Route::get('destroyPriority/{id}', 'Dmihd\PriorityController@destroy');

Route::get('getUserTicket', 'Dmihd\UserTicketController@index');
Route::post('storeUserTicket', 'Dmihd\UserTicketController@store');
Route::get('destroyUserTicket/{id}', 'Dmihd\UserTicketController@destroy');
Route::post('editUserTicket ', 'Dmihd\UserTicketController@edit');
Route::get('getUserView', 'Dmihd\UserTicketController@getUserView');
Route::get('getUserSubArea/{id}', 'Dmihd\UserTicketController@getUserSubArea');


Route::post('storeSolicitud', 'Dmihd\TicketController@store');
Route::get('sendTickets', 'Dmihd\TicketController@sendTickets');
Route::get('requestTickets', 'Dmihd\TicketController@requestTickets');
Route::post('filterSendTicket', 'Dmihd\TicketController@filterSendTicket');
Route::post('filterRerquestTicket', 'Dmihd\TicketController@filterRerquestTicket');
Route::get('showTicket/{id}', 'Dmihd\TicketController@show');
//**Rutas CONTROL ASISTENCIA */
Route::prefix('controlAsistencia')->group(function(){

//*Recursos Humanos Routes**//
Route::get('getHorariosPersonal','Alfa\RecursosHumanos\HorariosPersonalController@getHorariosPersonal');
Route::get('getHourEntrance','Alfa\RecursosHumanos\CambioHorarioController@getHourEntrance');
Route::get('getHourFood','Alfa\RecursosHumanos\CambioHorarioController@getHourFood');
Route::get('getHorarioPendiente','Alfa\RecursosHumanos\CambioHorarioController@getHorarioPendiente');
Route::get('getHorariosMiPersonalAutorizar','Alfa\RecursosHumanos\AutorizarHorariosAreaController@getHorariosMiPersonalAutorizar');
Route::post('getHorariosPersonalAprobar','Alfa\RecursosHumanos\PanelHorariosAprobarController@getHorariosPersonalAprobar');
Route::post('autorizarHorarioPersonalPanel','Alfa\RecursosHumanos\PanelHorariosAprobarController@autorizarHorarioPersonal');
Route::post('autorizarJustification','Alfa\RecursosHumanos\JustificationController@autorizarJustification');
Route::post('rechazarJustification','Alfa\RecursosHumanos\JustificationController@rechazarJustification');

Route::post('rechazarHorarioPersonalPanel','Alfa\RecursosHumanos\PanelHorariosAprobarController@rechazarHorarioPersonal');
Route::post('autorizarHorarioPersonal','Alfa\RecursosHumanos\AutorizarHorariosAreaController@autorizarHorarioPersonal');
Route::post('rechazarHorarioPersonal','Alfa\RecursosHumanos\AutorizarHorariosAreaController@rechazarHorarioPersonal');
Route::post('addCambioHorarioPersonal','Alfa\RecursosHumanos\CambioHorarioController@addCambioHorarioPersonal');
Route::post('updateCambioHorarioPersonal','Alfa\RecursosHumanos\CambioHorarioController@updateCambioHorarioPersonal');
Route::get('getPersonalIntelisis','Alfa\RecursosHumanos\PersonalIntelisisController@getPersonalIntelisisAll');
Route::get('getUbications','Alfa\RecursosHumanos\PersonalIntelisisController@getUbications');
Route::get('updatePersonalIntelisisSP','Alfa\RecursosHumanos\PersonalIntelisisController@updatePersonalIntelisisSP');
Route::get('getDatosPersonalIntelisis','Alfa\RecursosHumanos\PersonalIntelisisController@getDatosPersonalIntelisis');
Route::get('getCatTimeStatus','Alfa\RecursosHumanos\HorariosPersonalController@getCatTimeStatus');
Route::get('getTypeJustification','Alfa\RecursosHumanos\JustificationController@getTypeJustification');
Route::get('getMyJustifications','Alfa\RecursosHumanos\JustificationController@getMyJustifications');
Route::get('getJustificationsMyPersonal','Alfa\RecursosHumanos\JustificationController@getJustificationsMyPersonal');
Route::get('getTypeJustificationActive','Alfa\RecursosHumanos\JustificationController@getTypeJustificationActive');
Route::get('getCommanding_staff','Alfa\RecursosHumanos\JustificationController@getCommanding_staff');
Route::get('getDaysOff','Alfa\RecursosHumanos\ReporteAsistenciaController@getDaysOff');

Route::post('addCatTimeStatus','Alfa\RecursosHumanos\HorariosPersonalController@addCatTimeStatus');
Route::post('addHorarioPersonal','Alfa\RecursosHumanos\HorariosPersonalController@addHorarioPersonal');
Route::post('addTypeJustification','Alfa\RecursosHumanos\JustificationController@addTypeJustification');
Route::post('addDaysOff','Alfa\RecursosHumanos\ReporteAsistenciaController@addDaysOff');
Route::post('updateDaysOff','Alfa\RecursosHumanos\ReporteAsistenciaController@updateDaysOff');
Route::post('updateCatTimeStatus','Alfa\RecursosHumanos\HorariosPersonalController@updateCatTimeStatus');
Route::post('updateTypeJustification','Alfa\RecursosHumanos\JustificationController@updateTypeJustification');
Route::post('getReporteAsistencia','Alfa\RecursosHumanos\ReporteAsistenciaController@getReporteAsistencia');
Route::post('getReporteAsistenciaMiPersonal','Alfa\RecursosHumanos\ReporteAsistenciaController@getReporteAsistenciaMiPersonal');
Route::post('addJustification','Alfa\RecursosHumanos\JustificationController@addJustification');
Route::post('addJustificationUser','Alfa\RecursosHumanos\JustificationController@addJustificationUser');
Route::post('getPersonalAttendance','Alfa\RecursosHumanos\ReporteAsistenciaController@getPersonalAttendance');
});

/** Locations */
Route::group(['prefix' => 'locations'], function () {
    Route::get('/fetch','Alfa\Dining\DiningController@getTools');
});


/** Dining */
Route::group(['prefix' => 'dining'], function () {
    Route::post('/menu/store','Alfa\Dining\DiningController@storeMenu');
    Route::post('/menu/close','Alfa\Dining\DiningController@closeMenu');
    Route::post('/order/store','Alfa\Dining\DiningController@storeOrder');
    Route::post('/order/product/offer','Alfa\Dining\DiningController@storeOffer');
    Route::get('/order/product/get-offer-products','Alfa\Dining\DiningController@getOfferProducts');
    Route::get('/order/product/get-current-product','Alfa\Dining\DiningController@getCurrentProduct');
    Route::get('/order/get-order','Alfa\Dining\DiningController@getCurrentOrder');
    Route::get('/order/get-all-orders','Alfa\Dining\DiningController@getOrders');
    Route::get('/order/show-pdf','Alfa\Dining\DiningController@showPdf');
    Route::get('/fetch','Alfa\Dining\DiningController@fetchMenu');
    Route::post('/menu/update','Alfa\Dining\DiningController@updateMenu');
    Route::post('/order/product/take','Alfa\Dining\DiningController@takeProduct');
    Route::get('/get-week-menu','Alfa\Dining\DiningController@getWeekMenu');
    Route::get('/order/general-pdf/{id}','Alfa\Dining\DiningController@getPdf');
    Route::get('/get-cat-food-type','Alfa\Dining\DiningController@getCatFoodType');
    Route::post('/order/upload-cashout','Alfa\Dining\DiningController@uploadCashout');
    Route::get('/cashout/get-user-cashout','Alfa\Dining\DiningController@getUserCashout');
	Route::get('payments/fetch','Alfa\Dining\DiningController@fetchPayments');
});

/** Accounting */
Route::group(['prefix' => 'accounting'], function () {
	Route::get('/fetch-companies','Alfa\Accounting\AccountingController@fetchCompanies');
	Route::get('/fetch-all-companies','Alfa\Accounting\AccountingController@fetchAllCompanies');
	Route::get('/fetch-e-accounting','Alfa\Accounting\AccountingController@fetchElectronicAccounting');
	Route::post("e-accounting/graphic","Alfa\Accounting\AccountingController@getEAccountingGraphic");
	Route::get('/fetch-overviews','Alfa\Accounting\AccountingController@fetchOverviews');
	Route::get('/fetch-personal','Alfa\Accounting\AccountingController@fetchPersonal');
	Route::get('/get-tools','Alfa\Accounting\AccountingController@getTools');
	Route::get('/get-e-status','Alfa\Accounting\AccountingController@getEStatus');
	Route::get('/get-cat-overviews','Alfa\Accounting\AccountingController@getCatOverviews');
	Route::post('/overviews/store','Alfa\Accounting\AccountingController@storeOverview');
    Route::post('/companies/store','Alfa\Accounting\AccountingController@storeCompany');
    Route::post('/companies/update','Alfa\Accounting\AccountingController@updateCompany');
    Route::post('/e-accounting/store','Alfa\Accounting\AccountingController@storeEAccounting');
    Route::post('/companies/delete','Alfa\Accounting\AccountingController@deleteCompany');
	Route::get("/get-companies","Alfa\Accounting\AccountingController@getCompanies");


	Route::prefix('monthly-closure')->group(function (){
		Route::get("/list","Alfa\Accounting\MonthlyClosureController@listMonthlyClosure");
		Route::post("/graphic","Alfa\Accounting\MonthlyClosureController@getGraphic");
		Route::post("add","Alfa\Accounting\MonthlyClosureController@addRecord");
		Route::post("update","Alfa\Accounting\MonthlyClosureController@updateRecord");
		Route::get("delete-file/{id}/{field}","Alfa\Accounting\MonthlyClosureController@deleteFile");
		Route::get("single/{id}","Alfa\Accounting\MonthlyClosureController@singleMonthlyClosure");
		Route::get("get-cutting-day","Alfa\Accounting\MonthlyClosureController@CuttingDay");

		/* *********************** FECHAS DE CORTE *********************** */
		Route::get("/cutoff-date-list","Alfa\Accounting\MonthlyClosureController@listCutOffDate");
		Route::get("/cutoff-date-graphics","Alfa\Accounting\MonthlyClosureController@graphicsCutOffDate");
		Route::post("/cutoff-date-edit","Alfa\Accounting\MonthlyClosureController@editCutOffDate");

	});

	Route::prefix('fiscal-preclosing')->group(function (){
		Route::get("/list","Alfa\Accounting\FiscalPreclosingController@listFiscalPreclosing");
		Route::post("add","Alfa\Accounting\FiscalPreclosingController@addRecord");
		Route::post("update","Alfa\Accounting\FiscalPreclosingController@updateRecord");
		Route::get("single/{id}","Alfa\Accounting\FiscalPreclosingController@singleFiscalPreclosing");
	});
	Route::get('getCompaniesLaw','Alfa\Accounting\LeyAntilavadoController@getCompaniesLaw');
	Route::post('getDataAntilavado','Alfa\Accounting\LeyAntilavadoController@getDataAntilavado');
    Route::post('addAntilavado','Alfa\Accounting\LeyAntilavadoController@addAntilavado');
    Route::post('save-bulk-load-anti-laundering','Alfa\Accounting\LeyAntilavadoController@saveBulkLoad');
    Route::post('updateAntilavado','Alfa\Accounting\LeyAntilavadoController@updateAntilavado');
	Route::get('/fetch-overviews-accounting','Alfa\Accounting\AccountingController@fetchOverviewsAccounting');
	Route::get('/fetch-interim','Alfa\Accounting\AccountingController@fetchInterimPayments');
	Route::post('/interim/store','Alfa\Accounting\AccountingController@storeInterimPayment');
});

Route::prefix('rh')->group(function(){

    /* *********************** FUNCIONES GENERICAS *********************** */
	Route::get("/users-signatures-behalf","GenericFunctionsController@listUsersSignaturesBehalf");
	Route::get("/check-sign-on-behalf/{seccion}/{plaza}","GenericFunctionsController@checkUserSignOnBehalf");
	Route::get("/location-list","Alfa\RecursosHumanos\RecursosHumanosController@listLocation");

	/* *********************** COORDINADOR POR UBICACION *********************** */
	Route::prefix('location-coordinator')->group(function(){
		Route::get("/list","Alfa\RecursosHumanos\RecursosHumanosController@listLocationCoordinator");
		Route::post("/add","Alfa\RecursosHumanos\RecursosHumanosController@saveRecord");
		Route::get("/delete/{id}","Alfa\RecursosHumanos\RecursosHumanosController@deleteLocationCoordinator");
		Route::get("/rh-staff-list","Alfa\RecursosHumanos\RecursosHumanosController@rhStaffList");
	});


	/* *********************** REEMPLAZO TEMPORAL DE COLABORADOR *********************** */
	Route::prefix('/replace-collaborator')->group(function(){
		Route::get('list',"Alfa\RecursosHumanos\RecursosHumanosController@listReplaceCollaborator");
		Route::post('add',"Alfa\RecursosHumanos\RecursosHumanosController@saveReplaceCollaborator");
		Route::post('delete',"Alfa\RecursosHumanos\RecursosHumanosController@deleteReplaceCollaborator");

	});



    /* *********************** PERMISOS DE TRABAJO *********************** */
    Route::prefix('work-permits')->group(function(){
        Route::get("/list","Alfa\RecursosHumanos\WorkPermitsController@listWorkPermits");
        Route::get("/single/{id}","Alfa\RecursosHumanos\WorkPermitsController@singleWorkPermits");
        Route::get("/type-permit-list","Alfa\RecursosHumanos\WorkPermitsController@permitTypeList");
        Route::post("work-permits-add","Alfa\RecursosHumanos\WorkPermitsController@workPermitAdd");
        Route::get("cancel/{id}","Alfa\RecursosHumanos\WorkPermitsController@cancelWorkPermit");
        Route::get("/send-mail/{id}","Alfa\RecursosHumanos\WorkPermitsController@sendConfirmationEmail");

        Route::get("/permit-concept-list/{id}","Alfa\RecursosHumanos\WorkPermitsController@permitConceptList");
        Route::post("sing-permit","Alfa\RecursosHumanos\WorkPermitsController@signWorkPermit");
        Route::get("authorize-permit-list","Alfa\RecursosHumanos\WorkPermitsController@authorizePermitList");
        Route::get("staff-permit-list","Alfa\RecursosHumanos\WorkPermitsController@staffPermitList");
        Route::get("general-report","Alfa\RecursosHumanos\WorkPermitsController@generalReport");
        Route::get("general-pdf","Alfa\RecursosHumanos\WorkPermitsController@generarPDFWorkPermit");
        Route::get("print-document/{id}","Alfa\RecursosHumanos\WorkPermitsController@printDocumentWorkPermit");

    });

	/* *********************** VACACIONES *********************** */
    Route::prefix('vacation')->group(function(){
        Route::get('/list', 'Alfa\RecursosHumanos\VacationController@listVacation');
        Route::post('/add',"Alfa\RecursosHumanos\VacationController@addRecord");
        Route::get("/single/{id}","Alfa\RecursosHumanos\VacationController@singleRecord");
        Route::get("cancel/{id}","Alfa\RecursosHumanos\VacationController@cancelRecord");

        Route::get('/get-data-vacation-days','Alfa\RecursosHumanos\VacationController@getDataVacationDays');
        Route::post("sing-document","Alfa\RecursosHumanos\VacationController@signDocument");
        Route::get("check-requested-vacation","Alfa\RecursosHumanos\VacationController@checkRequestedVacation");
		Route::get("authorize-vacation-list","Alfa\RecursosHumanos\VacationController@authorizeVacationList");
		Route::get("generate-pdf/{id}","Alfa\RecursosHumanos\VacationController@generarPDFRecord");
		Route::get("staff-vacation-list","Alfa\RecursosHumanos\VacationController@staffVacationList");
		Route::get("general-report","Alfa\RecursosHumanos\VacationController@generalReport");
        Route::get("print-document/{id}","Alfa\RecursosHumanos\VacationController@printDocumentVacation");

    });


    /* *********************** PERSONAL INTELISIS PROFILE *********************** */
    Route::get("/personal-intelisis/profile","Alfa\RecursosHumanos\PersonalIntelisisController@getProfile");
    Route::get("/personal-intelisis/all-active","Alfa\RecursosHumanos\RecursosHumanosController@getAllUsersActive");








});

Route::prefix('tools')->group(function(){
    Route::get("/get-payroll/{year}","ToolsController@getPayroll");
    Route::post("payroll/get-pdf","ToolsController@getFilePayroll");
    Route::post("payroll/get-xml","ToolsController@getXmlPayroll");
    Route::get("/token-adhoc","GenericFunctionsController@getTokenUserAdhoc");
});

Route::prefix('intelisis')->group(function (){
    Route::get('/get-holidays','Alfa\RecursosHumanos\PersonalIntelisisController@getHolidays');
});
//**Rutas PERSONAL REQUISITION*/
Route::prefix('personalRequisition')->group(function(){

    Route::get('getAllCompanyName','Alfa\PersonalRequisitions\PersonalRequisitionController@getCompany_Sucursal');
    Route::get('getAllBranch_Code','Alfa\PersonalRequisitions\PersonalRequisitionController@getAllBranch_Code');
    Route::get('getAllCommanding_staff','Alfa\PersonalRequisitions\PersonalRequisitionController@getAllCommanding_staff');
    Route::post('getAllCommanding_staff_Panel','Alfa\PersonalRequisitions\PersonalRequisitionController@getAllCommanding_staff_Panel');
    Route::get('getCommanding_staff','Alfa\PersonalRequisitions\PersonalRequisitionController@getCommanding_staff');
    Route::get('getEmailDomain','Alfa\PersonalRequisitions\PersonalRequisitionController@getEmailDomain');
    Route::get('getSuperValidator','Alfa\PersonalRequisitions\PersonalRequisitionController@getSuperValidator');
    Route::get('getStatusRecruitment','Alfa\PersonalRequisitions\PersonalRequisitionController@getStatusRecruitment');
    Route::get('getPersonalRequisitionValidation','Alfa\PersonalRequisitions\PersonalRequisitionController@getPersonalRequisitionValidation');
    Route::post('getConsultaPersonalRequisitions','Alfa\PersonalRequisitions\PersonalRequisitionController@getConsultaPersonalRequisitions');
    Route::post('getConsultaPersonalRequisitionsSearch','Alfa\PersonalRequisitions\PersonalRequisitionController@fetchRequisitionsConsulta');
    Route::post('getAutRechPersonalRequisitions','Alfa\PersonalRequisitions\PersonalRequisitionController@getAutRechPersonalRequisitions');
    Route::post('getAutRechPersonalRequisitionsSearch','Alfa\PersonalRequisitions\PersonalRequisitionController@fetchRequisitionsReclutamiento');
    Route::get('getMyPersonalRequisitions','Alfa\PersonalRequisitions\PersonalRequisitionController@getMyPersonalRequisitions');
    Route::get('getRequisitionsMyPersonal','Alfa\PersonalRequisitions\PersonalRequisitionController@getRequisitionsMyPersonal');
    Route::get('getRequisitionsMyPersonalPendientes','Alfa\PersonalRequisitions\PersonalRequisitionController@getRequisitionsMyPersonalPendientes');
    Route::post('getRequisitionValidatebyId','Alfa\PersonalRequisitions\PersonalRequisitionController@getRequisitionValidatebyId');
    Route::post('GenerateRequisitionTemp','Alfa\PersonalRequisitions\PersonalRequisitionController@GenerateRequisitionTemp');
    Route::post('getStaff_Higher','Alfa\PersonalRequisitions\PersonalRequisitionController@getStaff_Higher');
    Route::post('getStaff_HigherConsulta','Alfa\PersonalRequisitions\PersonalRequisitionController@getStaff_HigherConsulta');
    Route::post('addPersonalRequisition','Alfa\PersonalRequisitions\PersonalRequisitionController@addPersonalRequisition');
    Route::post('addRequisition','Alfa\PersonalRequisitions\PersonalRequisitionController@addPersonalRequisition');
    Route::post('updatePersonalRequisition','Alfa\PersonalRequisitions\PersonalRequisitionController@updatePersonalRequisition');
    Route::post('updateStatusRecruitment','Alfa\PersonalRequisitions\PersonalRequisitionController@updateStatusRecruitment');
    Route::post('validatePersonalRequisition','Alfa\PersonalRequisitions\PersonalRequisitionController@validatePersonalRequisition');
    Route::post('CancelPersonalRequisition','Alfa\PersonalRequisitions\PersonalRequisitionController@CancelPersonalRequisition');
    Route::post('SignPersonalRequisition','Alfa\PersonalRequisitions\PersonalRequisitionController@SignPersonalRequisition');
	Route::post('AutorizeRequisition','Alfa\PersonalRequisitions\PersonalRequisitionController@AutorizeRequisition');

    Route::get("generete-pdf-personal-requisition/{idRequisition}","Alfa\PersonalRequisitions\PersonalRequisitionController@apiGeneretePDFPersonalRequisition");
});

Route::prefix('suppliers')->group(function (){
    Route::post('/ExistRFC','Alfa\Proveedores\SupplierRegistrationController@ExistRFC');
    Route::get('/getCountries','Alfa\Proveedores\SupplierRegistrationController@getCountries');
    Route::get('/getSpecialities','Alfa\Proveedores\SupplierRegistrationController@getSpecialities');
    Route::get('/getStates','Alfa\Proveedores\SupplierRegistrationController@getStates');
    // Route::post('/addSupplier','Alfa\Proveedores\SupplierRegistrationController@addSupplierprueba');
    Route::post('/addSupplier','Alfa\Proveedores\SupplierRegistrationController@addSupplier');
    Route::post('/addSupplierWeb','Alfa\Proveedores\SupplierRegistrationController@addSupplierWeb');
    Route::post('/DeleteSupplierWeb','Alfa\Proveedores\SupplierRegistrationController@DeleteSupplierWeb');
    Route::post('/DeleteFilesSupplierWeb','Alfa\Proveedores\SupplierRegistrationController@DeleteFilesSupplierWeb');
    Route::get('/getReportAccessSupplier','Alfa\Proveedores\SupplierRegistrationController@getReportAccessSupplier');
    Route::get('/getMySuppliers','Alfa\Proveedores\SupplierRegistrationController@getMySuppliers');
    Route::post('/getSuppliersEFO','Alfa\Proveedores\SupplierRegistrationController@getSuppliersEFO');
    Route::post('/getStatesbyCountry','Alfa\Proveedores\SupplierRegistrationController@getStatesbyCountry');
    Route::get('/getBanks','Alfa\Proveedores\SupplierRegistrationController@getBanks');
    Route::post('/getSuppliersEFOSearch','Alfa\Proveedores\SupplierRegistrationController@fetchSuppliers');
	Route::post('/updateSupplier','Alfa\Proveedores\SupplierRegistrationController@updateSupplier');
    Route::post('/updateStatusEFO','Alfa\Proveedores\SupplierRegistrationController@updateStatusEFO');
    Route::get('/get-total-data','Alfa\Suppliers\SuppliersController@getTotalData');
    Route::get('/fetch','Alfa\Suppliers\SuppliersController@fetchSuppliers');
    Route::get('/fetch-all','Alfa\Suppliers\SuppliersController@fetchAllSuppliers');
    Route::get('/fetch-specialties','Alfa\Suppliers\SuppliersController@fetchSpecialties');
	Route::post('/approve','Alfa\Suppliers\SuppliersController@approve');
	Route::post('/reactive','Alfa\Suppliers\SuppliersController@reactive');
	Route::post('/cancel','Alfa\Suppliers\SuppliersController@cancel');
	Route::post('/store-specialties','Alfa\Suppliers\SuppliersController@storeSpecialties');
	Route::post('/change-type','Alfa\Suppliers\SuppliersController@changeType');
    Route::post('/specialties/delete','Alfa\Suppliers\SuppliersController@deleteSpecialty');
    Route::post('/specialties/store','Alfa\Suppliers\SuppliersController@StoreSpecialty');
    Route::get('/fetch-cat-specialties','Alfa\Suppliers\SuppliersController@fecthCatSpecialties');
	Route::post('/update','Alfa\Suppliers\SuppliersController@update');
	Route::post('/show-pdf','Alfa\Suppliers\SuppliersController@showPdf');
	Route::get('/test-pdf/{id}','Alfa\Suppliers\SuppliersController@testPDF');
	Route::get('/export-to-excel','Alfa\Suppliers\SuppliersController@exportExcel');
	Route::post('/remove','Alfa\Suppliers\SuppliersController@remove');
	Route::post('/getBackSupplier','Alfa\Suppliers\SuppliersController@getBackSupplier');

	Route::get('supplier-documents','Alfa\Suppliers\SuppliersController@getSupplierDocuments');
	Route::get('delete-file/{document_id}','Alfa\Suppliers\SuppliersController@deleteSupplierDocument');
});
