<?php

use App\Services\PdfWrapper;
use Illuminate\Http\Response;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Generate PDFs in Laravel: A Deep Dive into Laravel PDF Export with Spatie/Browsershot - https://www.youtube.com/watch?v=320vwRDqi9w&t=184s
/**
 * Todo se configuró en la clase App\Services\PdfWrapper
 */
Route::get('/', function () {
  return (new PdfWrapper)
    ->loadView('exports.example', [
      'title' => 'Ejemplo exportar con Browsershot (PdfWrapper)',
    ])
    // Opciones para la descarga/visualización del archivo
    // ->save('example.pdfwrapper.pdf');
    ->download('example.pdfwrapper.pdf');
    // ->stream('example.pdfwrapper.pdf');
      
    // Personalizar el encabezado y pie de página
    /* ->loadHtml("<h1>Ejemplo exportar con Browsershot</h1>")
    ->loadHeaderHtml("<h1 style='font-size: 14px'>Encabezado</h1>")
    ->loadFooterHtml("<h1 style='font-size: 14px'>Pie de página</h1>")
    ->stream('example.pdfwrapper.pdf'); */
});

Route::get('/grafico', function () {
  return (new PdfWrapper)
    ->loadView('exports.charts')
    // Sin encabezado y pie de página
    ->loadHeaderHtml("<p></p>")
    ->loadFooterHtml("<p></p>")
    ->stream('example.graficoGoogle.pdf');
});

/* Route::get('/', function () {
  // Browsershot::url('https://laravel.com')
  Browsershot::html("<h1>Tutorial de Laravel</h1>")
    // ->setIncludePath('$PATH:/home/soporte/.nvm/versions/node/v20.17.0/bin')
    ->setIncludePath(config('services.browsershot.include_path'))
    // ->save('example.pdf');
    ->savePdf('laravel.pdf');
  
  return view('welcome');
}); */
Route::get('/exportar', function () {
  $pdf = Browsershot::html("<h1>Tutorial de Laravel</h1>")
    ->setIncludePath(config('services.browsershot.include_path'))
    ->pdf();
  
  // Descargar el archivo
  /* return new Response($pdf, 200, [
    'Content-Type' => 'application/pdf',
    'Content-Disposition' => 'attachment; filename="example.pdf',
    'Content-Length' => strlen($pdf)
  ]); */

  // Ver el archivo en el navegador
  return new Response($pdf, 200, [
    'Content-Type' => 'application/pdf',
    'Content-Disposition' => 'inline; filename="example.pdf'
  ]);

  return view('welcome');
});
// Exportar mediante plantilla Blade
Route::get('/plantilla', function () {
  $html = view('exports.example', [
    'title' => 'Ejemplo para exportar'
  ])->render();
  $headerHtml = view('exports._header')->render();
  $footerHtml = view('exports._footer')->render();

  $pdf = Browsershot::html($html)
    ->setIncludePath(config('services.browsershot.include_path'))
    ->paperSize(100, 100)
    ->format('A4')
    ->margins(30, 15, 30, 15)
    ->showBrowserHeaderAndFooter()
    ->headerHtml($headerHtml)
    ->footerHtml($footerHtml)
    ->waitUntilNetworkIdle()
    ->pdf();

  // Ver el archivo en el navegador
  return new Response($pdf, 200, [
    'Content-Type' => 'application/pdf',
    'Content-Disposition' => 'inline; filename="example.pdf'
  ]);

  return view('welcome');
});


Route::get('/dashboard', function () {
  return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
